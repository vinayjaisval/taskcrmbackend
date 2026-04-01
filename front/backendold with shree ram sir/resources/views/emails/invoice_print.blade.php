<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Print</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.css'>

<style type="text/css">
.invoice {
    background: #fff;
    padding: 0;
}

.invoice-company {
    font-size: 20px
}
.table thead th{
    white-space: nowrap;
}
.invoice-header {
    background: #f0f3f4;
    padding: 20px
}
.invoice-from,
.invoice-to {
    padding-right: 20px
}
.invoice-date .date,
.invoice-from strong,
.invoice-to strong {
    font-size: 16px;
    font-weight: 600
}

.invoice-date {
    text-align: right;
    padding-left: 20px
}
.pull-right {
    float: right;
}
.invoice-price {
    background: #f0f3f4;
    display: table;
    width: 100%
}
.invoice-price .invoice-price-left,
.invoice-price .invoice-price-right {    
    padding: 20px;
    font-size: 20px;
    font-weight: 600;
    width: 75%;
    position: relative;
    vertical-align: middle
}

.invoice-price .invoice-price-left .sub-price {
    vertical-align: middle;
    padding: 0 20px
}

.invoice-price small {
    font-size: 12px;
    font-weight: 400;
    display: block
}
.invoice-price .invoice-price-row {
    float: left
}

.invoice-price .invoice-price-right {
    width: 25%;
    background: #2d353c;
    color: #fff;
    font-size: 28px;
    text-align: right;
    vertical-align: bottom;
    font-weight: 300
}

.invoice-price .invoice-price-right small {
    display: block;
    opacity: .6;
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 12px
}

.invoice-footer {
    border-top: 1px solid #ddd;
    padding-top: 10px;
    font-size: 10px
}

.invoice-note {
    color: #999;
    margin-top: 20px;
    font-size: 85%
}
.invoice>div:not(.invoice-footer) {
    margin-bottom: 20px
}

.btn.btn-white, .btn.btn-white.disabled, .btn.btn-white.disabled:focus, .btn.btn-white.disabled:hover, .btn.btn-white[disabled], .btn.btn-white[disabled]:focus, .btn.btn-white[disabled]:hover {
    color: #2d353c;
    background: #fff;
    border-color: #d9dfe3;
}
</style>

</head>
<body>
    <div class="invoice">
     <!-- begin invoice-company -->
     <div class="invoice-company text-inverse f-w-600">
        EKON Solutions India Private Limited
     </div>
     <!-- end invoice-company -->
     <!-- begin invoice-header -->
     <div class="invoice-header">
        <div class="invoice-from">
           <small>from</small>
           <p>
              <strong class="text-inverse">EKON Solutions India Private Limited</strong><br>
              Pocket D, Dr Ambedkar Colony, Chhatarpur<br>
              Phone: +919810145459<br>
              Email: info@ekonindia.com
           </p>
        </div>
        <div class="invoice-to">
           <small>to</small>
           <p>
               <strong class="text-inverse">{{ $student->name }}</strong><br>
              {{ $student->address }}<br>
              {{ $student->city }}, {{ $student->zip }}<br>
              Phone: {{ $student->phone_number }}<br>
           </p>
        </div>
        <div class="invoice-date">
           <small>Invoice / {{ $month_name }} period</small>
           <div class="date text-inverse m-t-5">{{ $month_name }} {{ $day }}, {{ $year }}</div>
        </div>
     </div>
     <!-- end invoice-header -->
     <!-- begin invoice-content -->
     <div class="invoice-content">
        <!-- begin table-responsive -->
        <div class="table-responsive">
           <table class="table table-invoice">
              <thead>
                 <tr>
                    <th width="40%">Item Name</th>
                    <th width="20%">Amount</th>
                    <th width="20%">GST Amount</th>
                    <th width="20%">Total Amount</th>
                 </tr>
              </thead>
              <tbody>
                 <tr>
                    <td>
                       <span class="">{{ $appliedApplication->program->name }}</span><br>
                       <small>{{ $appliedApplication->program->school->university_name }}</small>
                    </td>
                    <td class="">
                        @if($appliedApplication->program->application_fee == 0)
                            Free
                        @else
                            @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                                USD
                            @else
                                {{ $appliedApplication->program->currency }}
                            @endif
                            {{ $appliedApplication->converted_application_fee_amount }}
                        @endif
                    </td>
                    <td class="">
                        ---
                    </td>
                    <td class="">
                        @if($appliedApplication->program->application_fee == 0)
                            Free
                        @else
                            @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                                USD
                            @else
                                {{ $appliedApplication->program->currency }}
                            @endif
                            {{ $appliedApplication->converted_application_fee_amount }}
                        @endif
                    </td>
                 </tr>
                 @if($appliedApplication->add_on_services_fee != '' || $appliedApplication->add_on_services_fee != NULL)
                 <tr>
                    <td>
                       <span class="">Add-on Service</span><br>
                       <small>{{ $appliedApplication->add_on_services_fee_details->fees_type }}</small>
                    </td>
                    <td class="">
                        @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                            USD
                        @else
                            {{ $appliedApplication->program->currency }}
                        @endif
                        {{ $appliedApplication->add_on_service_fee_amount }}
                    </td>
                    <td class="">
                        @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                            USD
                        @else
                            {{ $appliedApplication->program->currency }}
                        @endif
                        {{ $appliedApplication->add_on_service_fee_gst_amount - $appliedApplication->add_on_service_fee_amount }}
                    </td>
                    <td class="">
                        @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                            USD
                        @else
                            {{ $appliedApplication->program->currency }}
                        @endif
                        {{ $appliedApplication->add_on_service_fee_gst_amount }}
                    </td>
                 </tr>
                 @endif
                 <tr>
                    <td>
                       <span class="">Total</span><br>
                    </td>
                    <td class="">
                    </td>
                    <td class="">
                    </td>
                    <td class="">
                        @if($appliedApplication->program->currency == "" || $appliedApplication->program->currency == null)
                            USD
                        @else
                            {{ $appliedApplication->program->currency }}
                        @endif
                        {{ $appliedApplication->total_amount }}
                    </td>
                 </tr>
              </tbody>
           </table>
        </div>
     </div>
     <!-- end invoice-content -->
     <!-- begin invoice-note -->
     <div class="invoice-note">
        * Make all cheques payable to EKON Solutions India Private Limited<br>
        * Payment is due within 30 days<br>
        * If you have any questions concerning this invoice, contact +919810145459, info@ekonindia.com
     </div>
     <!-- end invoice-note -->
     <!-- begin invoice-footer -->
     <div class="invoice-footer">
        <p class="text-center m-b-5 f-w-600">
           THANK YOU FOR YOUR BUSINESS
        </p>
        <p class="text-center">
           <span>https://overseaseducationlane.com/</span>
           <span style="padding-left: 20px; height: 20px; width: 10px;"></span>
           <span>T:+919810145459</span>
           <span style="padding-left: 20px; height: 20px; width: 10px;"></span>
           <span>info@ekonindia.com</span>
        </p>
     </div>
     <!-- end invoice-footer -->
    </div>
</body>
</html>