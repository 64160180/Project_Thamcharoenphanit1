<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ปฏิทิน</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/lang/th.js'></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            height: calc(100vh - 150px);
            overflow: hidden;
        }
        /* ปุ่มเพิ่มรายงาน */
        #addReportButton {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            width: 200px;
        }
        #addReportButton:hover {
            background-color: #0056b3;
        }
        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .event-table th, .event-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .event-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <a href="calendar_form_add.php" id="addReportButton">เพิ่มรายงานปฏิทิน</a>
    <div class="container">
        <div id='calendar'></div>
        <h2>รายงานปฏิทิน</h2>
        <table class="event-table">
            <thead>
                <tr>
                    <th>ชื่อผู้สั่งซื้อ</th>
                    <th>เริ่ม</th>
                    <th>สิ้นสุด</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $pdo = new PDO("mysql:host=db;dbname=inventory_db;charset=utf8", "user", "user_password");
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $pdo->query("SELECT * FROM tbl_event");
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($events as $event) {
                        if ($event['end']) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($event['title']) . '</td>';
                            echo '<td>' . htmlspecialchars($event['start']) . '</td>';
                            echo '<td>' . htmlspecialchars($event['end']) . '</td>';
                            echo '<td>
                                    <button class="btn btn-danger btn-sm" onclick="deleteEvent(' . $event['id'] . ')">ลบ</button>
                                    <button class="btn btn-warning btn-sm" onclick="showEditModal(' . $event['id'] . ', \'' . htmlspecialchars($event['title']) . '\', \'' . htmlspecialchars($event['start']) . '\', \'' . htmlspecialchars($event['end']) . '\')">แก้ไข</button>
                                  </td>';
                            echo '</tr>';
                        }
                    }
                } catch (PDOException $e) {
                    echo 'การเชื่อมต่อล้มเหลว: ' . $e->getMessage();
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- โมดัลสำหรับการแก้ไขกิจกรรม -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">แก้ไข</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId">
                    <div class="form-group">
                        <label for="eventTitle">ชื่อผู้สั่งซื้อ</label>
                        <input type="text" class="form-control" id="eventTitle">
                    </div>
                    <div class="form-group">
                        <label for="eventStart">เริ่ม</label>
                        <input type="datetime-local" class="form-control" id="eventStart">
                    </div>
                    <div class="form-group">
                        <label for="eventEnd">สิ้นสุด</label>
                        <input type="datetime-local" class="form-control" id="eventEnd">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="updateEvent()">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                lang: 'th',
                editable: false,
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: 'calendar_add_main.php', // ที่อยู่ไฟล์ที่ส่งข้อมูลอีเวนต์
                        dataType: 'json',
                        success: function(data) {
                            var events = [];
                            $(data).each(function() {
                                if (this.end) {
                                    events.push({
                                        id: this.id,
                                        title: this.title,
                                        start: this.start, // วันเริ่มต้นและวันสิ้นสุดต้องตรงกัน
                                        end: this.end,
                                        allDay: true
                                    });
                                }
                            });
                            callback(events); // ส่งข้อมูลกิจกรรมไปยังปฏิทิน
                        },
                        error: function() {
                            alert("ไม่สามารถดึงข้อมูลกิจกรรมได้");
                        }
                    });
                },
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                }
            });
        });

        function deleteEvent(eventId) {
            if (confirm("คุณต้องการลบกิจกรรมนี้?")) {
                $.ajax({
                    url: 'calendar_add_main.php',
                    type: 'DELETE',
                    contentType: 'application/json', // กำหนด header เป็น JSON
                    data: JSON.stringify({ id: eventId }), // แปลงข้อมูลเป็น JSON
                    success: function(response) {
                        if (response.success) {
                            alert("ลบกิจกรรมเรียบร้อยแล้ว");
                            location.reload();
                        } else {
                            alert("เกิดข้อผิดพลาด: " + (response.error || "ไม่ทราบสาเหตุ"));
                        }
                    },
                    error: function(err) {
                        alert("เกิดข้อผิดพลาด: " + (err.responseText || "ไม่ทราบสาเหตุ"));
                    }
                });
            }
        }

        function showEditModal(eventId, title, start, end) {
            $('#eventId').val(eventId);
            $('#eventTitle').val(title);
            $('#eventStart').val(start);
            $('#eventEnd').val(end);
            $('#editEventModal').modal('show');
        }

        function updateEvent() {
            var eventId = $('#eventId').val();
            var title = $('#eventTitle').val();
            var start = $('#eventStart').val();
            var end = $('#eventEnd').val();

            $.ajax({
                url: 'calendar_add_main.php',
                type: 'PUT',
                contentType: 'application/json', // กำหนด header เป็น JSON
                data: JSON.stringify({ id: eventId, title: title, start: start, end: end }), // แปลงข้อมูลเป็น JSON
                success: function(response) {
                    if (response.success) {
                        alert("อัปเดตกิจกรรมเรียบร้อยแล้ว");
                        location.reload();
                    } else {
                        alert("เกิดข้อผิดพลาด: " + response.error);
                    }
                },
                error: function(err) {
                    alert("เกิดข้อผิดพลาด: " + err.responseText);
                }
            });
        }
    </script>
</body>
</html>
