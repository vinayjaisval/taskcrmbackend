<!DOCTYPE html>
<html>
<head>
<title>Report</title>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}

/* SCROLL CONTAINER */
.container1 {
    height: 800px;
    overflow-y: auto;
    border: 1px solid #ccc;
}

/* TABLE */
table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    background: #fff;
}

/* HEADER FIX */
thead th {
    position: sticky;
    top: 0;
    background: #000;
    color: #fff;
    z-index: 10;
}

/* CELL STYLE */
.table td, .table th {
    padding: 8px;
    font-size: 13px;
    vertical-align: middle;
}

/* NAME COLUMN LEFT */
.table td:nth-child(2) {
    text-align: left;
    font-weight: 600;
}

/* TEXT WRAP */
.table td {
    word-break: break-word;
    white-space: normal;
}

/* TEAM HEADER */
.team-row td {
    background: #dcdcdc;
    font-weight: bold;
    text-align: center;
}

/* NO TASK */
.no-task {
    color: red;
    font-weight: bold;
}
</style>

</head>
<body>

@php
$page = 'http://'.$_SERVER['HTTP_HOST']."/backend/report";
header("Refresh: 600; url=$page");

/* FILTER */
$whereCondition = "";

if(isset($_GET['id'])){
    $whereCondition = " AND lead_by = '".$_GET['id']."'";
}
else if(isset($_GET['project'])){
    $lead = DB::select("SELECT lead_by FROM tbl_users WHERE id='".$_GET['project']."'");
    $whereCondition = " AND lead_by = '".$lead[0]->lead_by."'";
}

/* ADMIN FETCH */
$admins = DB::select("SELECT id,name FROM tbl_users WHERE user_type='admin' AND is_deleted=0");
@endphp

<div class="container-fluid mt-2">
<div class="container1">

<table class="table table-bordered">

<colgroup>
<col style="width:5%">
<col style="width:18%">
<col style="width:7%">
<col style="width:7%">
<col style="width:10%">
<col style="width:10%">
<col style="width:8%">
<col style="width:10%">
<col style="width:10%">
<col style="width:10%">
</colgroup>

<thead>
<tr>
<th>SNo</th>
<th>UserName</th>
<th>TotalTask</th>
<th>DelayTask</th>
<th>9:30-11:30</th>
<th>11:30-01:00</th>
<th>01:00-02:00</th>
<th>02:00-03:30</th>
<th>03:30-05:00</th>
<th>05:00-06:30</th>
</tr>
</thead>

<tbody>

@foreach($admins as $admin)

<tr class="team-row">
<td colspan="10">{{ $admin->name }}</td>
</tr>

@php
$users = DB::select("SELECT id,name FROM tbl_users 
WHERE user_type='agent' 
AND lead_by='".$admin->id."' 
AND is_deleted=0 ".$whereCondition);

$i=1;
@endphp

@foreach($users as $usr)

<tr>
<td>{{$i}}</td>
<td>{{$usr->name}}</td>

<!-- TOTAL -->
<td>
@php
$c = DB::select("SELECT count(id) as c FROM tbl_lead WHERE FIND_IN_SET($usr->id,assignee)");
echo $c[0]->c;
@endphp
</td>

<!-- DELAY -->
<td>
@php
$curDate = date('Y-m-d', strtotime("-1 day"));
$c = DB::select("SELECT count(id) as c FROM tbl_lead 
WHERE FIND_IN_SET($usr->id,assignee)
AND dedline < '".$curDate." 23:59:00'
AND status != 3");

echo ($c[0]->c>0) 
? "<span class='no-task'>".$c[0]->c."</span>" 
: "-";
@endphp
</td>

<!-- SLOT 1 -->
<td>
@php
$d = DB::select("SELECT c.name,c.total_time_assign FROM tbl_lead_follow_timer a,tbl_lead c 
WHERE a.lead_id=c.id AND a.user_id='".$usr->id."'
AND (a.updatetime BETWEEN '".date('Y-m-d 09:30:00')."' AND '".date('Y-m-d 11:30:00')."'
OR a.updatetimes BETWEEN '".date('Y-m-d 09:30:00')."' AND '".date('Y-m-d 11:30:00')."')
ORDER BY a.id DESC LIMIT 1");
@endphp
{!! $d ? "<div>".$d[0]->name."</div><small>".$d[0]->total_time_assign." Min</small>" : "<span class='no-task'>No Task</span>" !!}
</td>

<!-- SLOT 2 -->
<td>
@php
$d = DB::select("SELECT c.name,c.total_time_assign FROM tbl_lead_follow_timer a,tbl_lead c 
WHERE a.lead_id=c.id AND a.user_id='".$usr->id."'
AND (a.updatetime BETWEEN '".date('Y-m-d 11:30:00')."' AND '".date('Y-m-d 13:00:00')."'
OR a.updatetimes BETWEEN '".date('Y-m-d 11:30:00')."' AND '".date('Y-m-d 13:00:00')."')
ORDER BY a.id DESC LIMIT 1");
@endphp
{!! $d ? "<div>".$d[0]->name."</div><small>".$d[0]->total_time_assign." Min</small>" : "<span class='no-task'>No Task</span>" !!}
</td>

<td><b>Lunch</b></td>

<!-- SLOT 3 -->
<td>
@php
$d = DB::select("SELECT c.name,c.total_time_assign FROM tbl_lead_follow_timer a,tbl_lead c 
WHERE a.lead_id=c.id AND a.user_id='".$usr->id."'
AND (a.updatetime BETWEEN '".date('Y-m-d 14:00:00')."' AND '".date('Y-m-d 15:30:00')."'
OR a.updatetimes BETWEEN '".date('Y-m-d 14:00:00')."' AND '".date('Y-m-d 15:30:00')."')
ORDER BY a.id DESC LIMIT 1");
@endphp
{!! $d ? "<div>".$d[0]->name."</div><small>".$d[0]->total_time_assign." Min</small>" : "<span class='no-task'>No Task</span>" !!}
</td>

<!-- SLOT 4 -->
<td>
@php
$d = DB::select("SELECT c.name,c.total_time_assign FROM tbl_lead_follow_timer a,tbl_lead c 
WHERE a.lead_id=c.id AND a.user_id='".$usr->id."'
AND (a.updatetime BETWEEN '".date('Y-m-d 15:30:00')."' AND '".date('Y-m-d 17:00:00')."'
OR a.updatetimes BETWEEN '".date('Y-m-d 15:30:00')."' AND '".date('Y-m-d 17:00:00')."')
ORDER BY a.id DESC LIMIT 1");
@endphp
{!! $d ? "<div>".$d[0]->name."</div><small>".$d[0]->total_time_assign." Min</small>" : "<span class='no-task'>No Task</span>" !!}
</td>

<!-- SLOT 5 -->
<td>
@php
$d = DB::select("SELECT c.name,c.total_time_assign FROM tbl_lead_follow_timer a,tbl_lead c 
WHERE a.lead_id=c.id AND a.user_id='".$usr->id."'
AND (a.updatetime BETWEEN '".date('Y-m-d 17:00:00')."' AND '".date('Y-m-d 18:30:00')."'
OR a.updatetimes BETWEEN '".date('Y-m-d 17:00:00')."' AND '".date('Y-m-d 18:30:00')."')
ORDER BY a.id DESC LIMIT 1");
@endphp
{!! $d ? "<div>".$d[0]->name."</div><small>".$d[0]->total_time_assign." Min</small>" : "<span class='no-task'>No Task</span>" !!}
</td>

</tr>

@php $i++; @endphp
@endforeach

@endforeach

</tbody>
</table>

</div>
</div>

<!-- AUTO SCROLL WITH HOVER STOP -->
<script>
let box = document.querySelector(".container1");
let scrollInterval;

function startScroll(){
    scrollInterval = setInterval(() => {
        box.scrollTop += 1;

        if (box.scrollTop >= box.scrollHeight - box.clientHeight) {
            box.scrollTop = 0;
        }
    }, 60);
}

function stopScroll(){
    clearInterval(scrollInterval);
}

startScroll();

box.addEventListener("mouseenter", stopScroll);
box.addEventListener("mouseleave", startScroll);
</script>

</body>
</html>