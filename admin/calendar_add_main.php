<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตัวอย่างการจัดการ DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // รับข้อมูลที่ส่งมา
        $data = json_decode(file_get_contents('php://input'), true);
        $eventId = $data['id'] ?? null; // รับ ID ของกิจกรรม

        if ($eventId) {
            // ดำเนินการลบกิจกรรม
            $stmt = $pdo->prepare("DELETE FROM tbl_event WHERE id = :id");
            $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'ไม่สามารถลบกิจกรรมได้']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ไม่พบ ID ของกิจกรรม']);
        }
        exit; // ออกจากสคริปต์หลังจากตอบกลับ
    }

    // ตรวจสอบว่าเป็นคำขอ PUT สำหรับการอัปเดตหรือไม่
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $data);
        $eventId = $data['id'] ?? null; // รับ id ของกิจกรรมที่ต้องการอัปเดต
        $title = $data['title'] ?? null;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;

        if ($eventId && $title && $start && $end) {
            // อัปเดตกิจกรรม
            $stmt = $pdo->prepare("UPDATE tbl_event SET title = :title, start = :start, end = :end WHERE id = :id");
            $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
                exit();
            } else {
                echo json_encode(['success' => false, 'error' => 'ไม่สามารถอัปเดตกิจกรรมได้']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบ']);
            exit();
        }
    }

    // ตรวจสอบว่าเป็นคำขอ POST สำหรับการเพิ่มกิจกรรม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // รับข้อมูลที่ส่งมา
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? null;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;

        if ($title && $start && $end) {
            // ดำเนินการเพิ่มกิจกรรม
            $stmt = $pdo->prepare("INSERT INTO tbl_event (title, start, end) VALUES (:title, :start, :end)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':start', $start);
            $stmt->bindParam(':end', $end);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'ไม่สามารถเพิ่มกิจกรรมได้']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบ']);
        }
        exit; // ออกจากสคริปต์หลังจากตอบกลับ
    }

    // ดึงข้อมูลกิจกรรม
    $stmt = $pdo->query("SELECT id, title, start, end FROM tbl_event");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งออกในรูปแบบ JSON
    $formatted_events = [];
    foreach ($events as $event) {
        $formatted_events[] = [
            'id' => $event['id'],
            'title' => $event['title'],
            'start' => $event['start'], // วันเริ่มต้น
            'end' => $event['end'], // วันสิ้นสุด
            'allDay' => true
        ];
    }

    echo json_encode($formatted_events);

} catch (PDOException $e) {
    echo json_encode(['error' => 'การเชื่อมต่อล้มเหลว: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
