@php
    $isEduThanzeel = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 6);
    $isESchool = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 5);
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $payment->invoice->invoice_number }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path("fonts/DejaVuSans.ttf") }}') format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        .rupee {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            /* font-weight: bold; */
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; line-height: 1.3; color: #000; background: white; margin: 0; padding: 5px;">
    <div style="width: 100%; max-width: 900px; margin: 0 auto; background: white;">
        
        <!-- Header Section -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: top; padding-right: 15px;">
                                @if($isEduThanzeel && file_exists(public_path('storage/eduthanzeel.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eduthanzeel.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @elseif($isESchool && file_exists(public_path('storage/eschool.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eschool.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @elseif(file_exists(public_path('storage/logo.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/logo.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @else
                                    <div style="height: 80px; width: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666; margin-top: 25px !important;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; font-size: 10px; line-height: 1.4;">
                        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                    <p style="margin: 2px 0;">PALATHINGAL, ULLANAM P.O,</p>
                    <p style="margin: 2px 0;">676303</p>
                    <p style="margin: 2px 0;">REG.OFFICE 2/421A, PANTHARANGADI PO</p>
                    <p style="margin: 2px 0;">676306</p>
                    <p style="margin: 2px 0;">Phone no.: 6282055715 Email: nisaskillpark@gmail.com</p>
                    <p style="margin: 2px 0;">GSTIN: 32AAECF7209B1Z7, State: 32-Kerala</p>
                </td>
            </tr>
        </table>

        <!-- Tax Invoice Title -->
                        <div style="text-align: center; color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; font-weight: bold; font-size: 18px; border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 10px 0;">Tax Invoice</div>

        <!-- Bill To and Invoice Details -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                    <h6 style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #000;">Bill To</h6>
                    <p style="margin: 3px 0; font-size: 11px;">Name: {{ $payment->invoice->student->name }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">Contact No.: {{ $payment->invoice->student->phone }}</p>
                </td>
                <td style="width: 48%; vertical-align: top; padding-left: 10px; text-align: right;">
                    <h6 style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #000;">Invoice Details</h6>
                    <p style="margin: 3px 0; font-size: 11px;">Invoice No.: {{ $payment->invoice->invoice_number }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">Date: {{ $payment->invoice->invoice_date->format('d-m-Y') }}</p>
                </td>
            </tr>
        </table>

        <!-- Itemized Table -->
        <div style="margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <thead style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white;">
                    <tr>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">#</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Item name</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">HSN/SAC</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Quantity</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Price/Unit</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">GST</th>
                        <th style="padding: 10px 8px; text-align: left; font-weight: bold; font-size: 10px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;">1</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px; font-weight: bold;">
                            @if($payment->invoice->invoice_type === 'course')
                                {{ $payment->invoice->course->title ?? 'N/A' }}
                            @elseif($payment->invoice->invoice_type === 'e-service')
                                {{ $payment->invoice->service_name ?? 'N/A' }}
                            @elseif($payment->invoice->invoice_type === 'batch_change')
                                Batch Change - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;">
                            @if($payment->invoice->invoice_type === 'course')
                                {{ $payment->invoice->course->code ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;">1</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount / 1.18, 2) }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><span class="rupee">₹</span> {{ number_format(($payment->invoice->total_amount / 1.18) * 0.18, 2) }} (18%)</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount, 2) }}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;" colspan="3"><strong>Total</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;">1</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount / 1.18, 2) }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><span class="rupee">₹</span> {{ number_format(($payment->invoice->total_amount / 1.18) * 0.18, 2) }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 10px;"><strong><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Main Content Area - Table Layout for PDF -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <!-- Left Column -->
                <td style="width: 48%; vertical-align: top; padding-right: 5px;">

                    <!-- Tax Type Section -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Tax Details</h6>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="padding: 6px 8px; font-size: 10px; font-weight: bold; background: white; color: #000; border: none; text-align: left;">Tax type</th>
                                    <th style="padding: 6px 8px; font-size: 10px; font-weight: bold; background: white; color: #000; border: none; text-align: left;">Taxable amount</th>
                                    <th style="padding: 6px 8px; font-size: 10px; font-weight: bold; background: white; color: #000; border: none; text-align: left;">Rate</th>
                                    <th style="padding: 6px 8px; font-size: 10px; font-weight: bold; background: white; color: #000; border: none; text-align: left;">Tax amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;">SGST</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount / 1.18, 2) }}</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;">9%</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;"><span class="rupee">₹</span> {{ number_format(($payment->invoice->total_amount / 1.18) * 0.09, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;">CGST</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount / 1.18, 2) }}</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;">9%</td>
                                    <td style="padding: 6px 8px; font-size: 10px; border: none;"><span class="rupee">₹</span> {{ number_format(($payment->invoice->total_amount / 1.18) * 0.09, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Invoice Amount In Words -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Invoice Amount In Words</h6>
                        <p style="margin: 4px 0; font-size: 10px;">{{ $payment->total_amount_in_words }} Rupees only</p>
                    </div>

                    <!-- Terms and Conditions -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Terms and Conditions</h6>
                        <p style="margin: 4px 0; font-size: 10px;">THIS AMOUNT IS NON REFUNDABLE</p>
                    </div>

                    <!-- Bank Details -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Bank Details</h6>
                        <p style="margin: 4px 0; font-size: 10px;">Name: AXIS BANK, KALLAI ROAD, KOZHIKODE</p>
                        <p style="margin: 4px 0; font-size: 10px;">Account No.: 921020041902527</p>
                        <p style="margin: 4px 0; font-size: 10px;">IFSC code: UTIB0001908</p>
                        <p style="margin: 4px 0; font-size: 10px;">Account holder's name: FUTURE AND TREE</p>
                    </div>
                </td>

                <!-- Right Column -->
                <td style="width: 48%; vertical-align: top; padding-left: 5px;">
                    
                    <!-- Amounts Section -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Amounts</h6>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Sub Total</td>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount / 1.18, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px;"><strong>Total</strong></td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right;"><strong><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0;">Received</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0;"><span class="rupee">₹</span> {{ number_format($payment->amount_paid, 2) }}</td>
                            </tr>
                            <!-- <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0;">Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount - $payment->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px;">Previous Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->previous_balance, 2) }}</td>
                            </tr> -->
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px;">Current Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->invoice->total_amount - $payment->invoice->paid_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Signature Section -->
                    <div style="text-align: center; margin-top: 60px;">
                        <p style="font-size: 10px; margin: 3px 0;">For: {{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</p>
                        <div style="height: 60px; margin: 10px 0; display: flex; align-items: center; justify-content: center;">
                            @if(file_exists(storage_path('app/public/accounts-sign.png')))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/accounts-sign.png'))) }}" alt="Signature" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                            @else
                                <div style="height: 60px; width: 150px; border-bottom: 1px solid #333;"></div>
                            @endif
                        </div>
                        <p style="font-size: 10px; margin: 3px 0;">Authorized Signatory</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
