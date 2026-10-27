<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond($status, $body) {
    $messages = array(200=>'OK', 400=>'Bad Request', 405=>'Method Not Allowed',
        413=>'Request Entity Too Large', 500=>'Internal Server Error');
    $message = isset($messages[$status]) ? $messages[$status] : 'OK';
    header('HTTP/1.1 ' . $status . ' ' . $message);
    echo json_encode($body);
    exit;
}

function table_map() {
    return array('pacientes'=>'pacientes', 'doctor'=>'doctores', 'citas'=>'citas',
        'insumos'=>'insumos', 'pagos'=>'pagos', 'odontogramas'=>'odontogramas');
}

function ensure_migrated($pdo) {
    $tables = table_map();
    $total = 0;
    foreach ($tables as $table) {
        $total += (int)$pdo->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
    }
    if ($total > 0) return;

    try {
        $old = $pdo->query('SELECT data_json FROM app_state WHERE id = 1')->fetch();
    } catch (Exception $e) {
        $old = false;
    }
    if (!$old || !isset($old['data_json'])) return;
    $data = json_decode($old['data_json'], true);
    if (!is_array($data)) return;

    $pdo->beginTransaction();
    try {
        foreach ($tables as $section => $table) {
            if (!isset($data[$section]) || !is_array($data[$section])) continue;
            $position = 0;
            foreach ($data[$section] as $key => $item) {
                if ($section === 'odontogramas') {
                    $recordKey = (string)$key;
                    $record = $item;
                } else {
                    $record = $item;
                    $recordKey = (is_array($item) && isset($item['id'])) ? (string)$item['id'] : (string)$key;
                }
                $stmt = $pdo->prepare('INSERT IGNORE INTO `' . $table . '` (record_key, position_no, data_json) VALUES (?, ?, ?)');
                $stmt->execute(array($recordKey, $position, json_encode($record)));
                $position++;
            }
        }
        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

function load_section($pdo, $table, $asObject) {
    $stmt = $pdo->query('SELECT record_key, data_json FROM `' . $table . '` ORDER BY position_no ASC');
    $result = array();
    while ($row = $stmt->fetch()) {
        $value = json_decode($row['data_json'], true);
        if ($asObject) $result[(string)$row['record_key']] = $value;
        else $result[] = $value;
    }
    return $result;
}

function save_section($pdo, $table, $items, $asObject) {
    $pdo->exec('DELETE FROM `' . $table . '`');
    $stmt = $pdo->prepare('INSERT INTO `' . $table . '` (record_key, position_no, data_json) VALUES (?, ?, ?)');
    $position = 0;
    foreach ($items as $key => $item) {
        if ($asObject) {
            $recordKey = (string)$key;
            $record = $item;
        } else {
            $record = $item;
            $recordKey = (is_array($item) && isset($item['id'])) ? (string)$item['id'] : (string)$key;
        }
        if ($recordKey === '') $recordKey = (string)$position;
        $json = json_encode($record);
        if ($json === false) throw new Exception('No se pudo codificar un registro.');
        $stmt->execute(array($recordKey, $position, $json));
        $position++;
    }
}

try {
    $config = require dirname(__FILE__) . '/config.php';
    $dsn = 'mysql:host=' . $config['host'] . ';port=' . $config['port'] .
           ';dbname=' . $config['database'] . ';charset=' . $config['charset'];
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    ensure_migrated($pdo);
    $method = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method === 'GET' && $action === 'notas') {
    $pacienteId = isset($_GET['paciente_id'])
        ? trim($_GET['paciente_id'])
        : '';

    if ($pacienteId === '') {
        respond(400, array(
            'ok' => false,
            'error' => 'Falta el identificador del paciente.'
        ));
    }

    $stmt = $pdo->prepare(
        'SELECT id, paciente_id, nota, fecha
         FROM notas_clinicas
         WHERE paciente_id = ?
         ORDER BY fecha DESC, id DESC'
    );

    $stmt->execute(array($pacienteId));

    respond(200, array(
        'ok' => true,
        'notas' => $stmt->fetchAll()
    ));
}

    if ($method === 'GET') {
        $data = array(
            'pacientes'=>load_section($pdo, 'pacientes', false),
            'doctor'=>load_section($pdo, 'doctores', false),
            'citas'=>load_section($pdo, 'citas', false),
            'insumos'=>load_section($pdo, 'insumos', false),
            'pagos'=>load_section($pdo, 'pagos', false),
            'odontogramas'=>load_section($pdo, 'odontogramas', true)
        );
        respond(200, array('ok'=>true, 'data'=>$data));
    }

    if ($method !== 'POST') {
        header('Allow: GET, POST');
        respond(405, array('ok'=>false, 'error'=>'Método no permitido.'));
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || strlen($raw) > 5242880)
        respond(413, array('ok'=>false, 'error'=>'Solicitud vacía o mayor a 5 MB.'));

    $data = json_decode($raw, true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE)
        respond(400, array('ok'=>false, 'error'=>'JSON inválido.'));
   
    if ($action === 'guardar_nota') {
        $pacienteId = isset($data['paciente_id'])
            ? trim((string)$data['paciente_id'])
            : '';

        $nota = isset($data['nota'])
            ? trim((string)$data['nota'])
            : '';

        if ($pacienteId === '' || $nota === '') {
            respond(400, array(
                'ok' => false,
                'error' => 'El paciente y la nota son obligatorios.'
            ));
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO notas_clinicas
                 (paciente_id, nota, fecha)
                 VALUES (?, ?, NOW())'
            );

            $stmt->execute(array($pacienteId, $nota));

            respond(200, array(
                'ok' => true,
                'message' => 'Nota clínica guardada correctamente.',
                'id' => $pdo->lastInsertId()
            ));
        } catch (PDOException $e) {
            error_log('DentPOS guardar_nota: ' . $e->getMessage());

            respond(500, array(
                'ok' => false,
                'error' => 'No se pudo insertar la nota en notas_clinicas.'
            ));
        }
    }


    $required = array('pacientes','doctor','citas','insumos','pagos','odontogramas');
    foreach ($required as $key) {
        if (!array_key_exists($key, $data) || !is_array($data[$key]))
            respond(400, array('ok'=>false, 'error'=>'Falta sección o formato inválido: ' . $key));
    }

    $pdo->beginTransaction();
    try {
        save_section($pdo, 'pacientes', $data['pacientes'], false);
        save_section($pdo, 'doctores', $data['doctor'], false);
        save_section($pdo, 'citas', $data['citas'], false);
        save_section($pdo, 'insumos', $data['insumos'], false);
        save_section($pdo, 'pagos', $data['pagos'], false);
        save_section($pdo, 'odontogramas', $data['odontogramas'], true);
        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    respond(200, array('ok'=>true, 'message'=>'Datos guardados en las tablas de DentPOS.'));
} catch (Exception $e) {
    error_log('DentPOS API: ' . $e->getMessage());
    respond(500, array('ok'=>false, 'error'=>'Error de conexión o guardado. Revisa config.php y que hayas importado database_tables.sql.'));
}
?>
