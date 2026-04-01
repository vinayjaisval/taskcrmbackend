@php

$curDate = date('Y-m-d');

@endphp


<html>
    <head>
        <title>Reports</title>
    </head>
    <body style="width: 580px; margin: 0 auto; border: 2px solid #069; padding: 20px; height: auto; border-radius: 20px;">
        
        <div style="width: 30%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            Today Task Created <br>
            
            <b>
                @php
                    $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND (created_at > '".$curDate." 00:00:00' AND created_at < '".$curDate." 23:59:00' )";
                    $taskCount=DB::select($sql);
                    echo $taskCount[0]->countID;
                @endphp
            </b>
        </div>
        
        
        
        <div style="width: 32%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            Today Task Completed <br>
            
           <b> 
           @php
                $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND status = 3 AND (created_at > '".$curDate." 00:00:00' AND created_at < '".$curDate." 23:59:00' )";
                $taskCount=DB::select($sql);
                echo $taskCount[0]->countID;
            @endphp
           </b>
        </div>
        
        <div style="width: 30%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            Today Task Updated <br>
            
           <b> @php
                $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND (updated_at > '".$curDate." 00:00:00' AND updated_at < '".$curDate." 23:59:00' )";
                $taskCount=DB::select($sql);
                echo $taskCount[0]->countID;
            @endphp </b>
        </div>
        
        <hr style="width: 100%; float: left;">
        
        <div style="width: 25%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            Total Task <br>
            
           <b> @php
                $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 ";
                $taskCount=DB::select($sql);
                echo $taskCount[0]->countID;
            @endphp  </b>
        </div>
        
        <div style="width: 25%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            TaskCompleted <br>
            
           <b>  @php
                $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND status = 3 ";
                $taskCount=DB::select($sql);
                echo $taskCount[0]->countID;
            @endphp  </b>
        </div>
        
         <div style="width: 25%; float: left; border: 1px solid #069; padding: 10px; border-radius: 20px; margin-right: 5px; margin-bottom: 5px; text-align:center">
            Task Delays <br>
           <b style="color: red;"> @php
                $deadline_date = date('Y-m-d h:i:s');
                $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND status != 3 AND `dedline` < '".$deadline_date."'";
                $taskCount=DB::select($sql);
                echo $taskCount[0]->countID;
            @endphp  </b>
        </div>
        
        <hr style="width: 100%; float: left;">
        
        <h4 style="margin: 0; padding: 0; margin-bottom: 10px; width: 100%; float: left;">Project Delay By Dates</h4>
        
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ccc;">Name</th>
                    <th style="border: 1px solid #ccc;">StartDate</th>
                    <th style="border: 1px solid #ccc;">EndDate</th>
                    <th style="border: 1px solid #ccc;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $projects = DB::select("SELECT a.id, a.name, a.status, a.start_date, a.end_date, b.name as stat_name FROM `tbl_users` a, tbl_source b WHERE 1 AND a.status = b.id AND user_type = 'project' AND a.status != '3' AND a.end_date < '".$curDate."' order by name ASC ");   
                        foreach($projects as $proj){
                @endphp
                <tr>
                    <td style="border: 1px solid #ccc;">{{$proj->name;}}</td>
                    <td style="border: 1px solid #ccc;">{{$proj->start_date}}</td>
                    <td style="border: 1px solid #ccc;">{{$proj->end_date}}</td>
                    <td style="border: 1px solid #ccc;">{{$proj->stat_name}}</td>
                </tr>
                @php } @endphp
            </tbody>
        </table>
        
        <hr style="width: 100%;">
        
        <h4 style="margin: 0; padding: 0; margin-bottom: 10px;">Delay Task By Members</h4>
        
        @php
            
            $sql = DB::select("SELECT id, name FROM `tbl_users` WHERE `user_type` = 'agent' AND is_deleted = 0 order by name ASC");
            foreach($sql as $usr){
            @endphp
                @php
                    $deadline_date = date('Y-m-d h:i:s');
                    $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND status != 3 AND `dedline` < '".$deadline_date."' AND FIND_IN_SET($usr->id, assignee)";
                    $taskCount=DB::select($sql);
                    if($taskCount[0]->countID > 0){ @endphp
                         <span style="border: 1px solid #ccc; padding: 5px 15px; display: inline-block; margin-right: 5px; border-radius: 10px;  margin-bottom: 5px;">{{$usr->name}} - <span style="color: red; font-size: 22px; font-weight: 700">{{$taskCount[0]->countID}}</span></span>
                    @php }
                @endphp
            
            @php } @endphp
            
            
            <hr style="width: 100%;">
        
        <h4 style="margin: 0; padding: 0; margin-bottom: 10px;">Total Task For Members</h4>
        
        @php
            
            $sql = DB::select("SELECT id, name FROM `tbl_users` WHERE `user_type` = 'agent' AND is_deleted = 0 order by name ASC");
            foreach($sql as $usr){
            @endphp
                @php
                    $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1  AND FIND_IN_SET($usr->id, assignee)";
                    $taskCount=DB::select($sql);
                    if($taskCount[0]->countID > 0){ @endphp
                         <span style="border: 1px solid #ccc; padding: 5px 15px; display: inline-block; margin-right: 5px; border-radius: 10px;  margin-bottom: 5px;">{{$usr->name}} - <span style="color: blue; font-size: 22px; font-weight: 700">{{$taskCount[0]->countID}}</span></span>
                    @php }
                @endphp
            
            @php } @endphp
        
        
        <hr style="width: 100%;">
        
        <h4 style="margin: 0; padding: 0; margin-bottom: 10px;">Members Not Working Today</h4>
        
        @php
            $projects = DB::select("SELECT name FROM tbl_users WHERE user_type = 'agent' AND is_deleted = '0' AND id NOT IN (SELECT user_id FROM tbl_lead_follow_timer WHERE created_at LIKE '%".$curDate."%' GROUP BY user_id) ORDER BY name ASC");   
                foreach($projects as $proj){
        @endphp
        
        <span style="border: 1px solid #ccc; padding: 5px 15px; display: inline-block; margin-right: 5px; border-radius: 10px; color: red; margin-bottom: 5px;">{{$proj->name;}}</span>
        @php } @endphp
        
        
    </body>
</html>

