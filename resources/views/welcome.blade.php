<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Making the Invoice for Zatcha</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container-fluid p-5 bg-primary text-white text-center">
          <h1>Zatcha E-Invoice</h1>
          <p>please insert info for invoice</p> 
        </div>
          
        <div class="container mt-5">
          <div class="row">
            <form action="{{ route('qrcode') }}" method="post">
              @csrf
              <div class="mb-3">
                  <label class="form-label" for="seller_name">Seller Name</label>
                  <input class="form-control form-control-lg" type="text" id="seller_name" name="seller_name" value="Seller">
              </div>
              <div class="mb-3">
                  <label class="form-label" for="vat_number">VAT Number</label>
                  <input class="form-control form-control-lg" type="text" id="vat_number" name="vat_number" value="310000000000000">
              </div>
              <div class="mb-3">
                  <label class="form-label" for="invoice_date">Date and Time</label>
                  <input class="form-control form-control-lg" type="datetime" id="invoice_date" name="invoice_date" value="2022-12-15 14:41:15">
              </div>
              <div class="mb-3">
                  <label class="form-label" for="total_amount">Total Amount (with VAT)</label>
                  <input class="form-control form-control-lg" type="text" id="total_amount" name="total_amount" value="2000.00">
              </div>
              <div class="mb-3">
                  <label class="form-label" for="vat_amount">VAT Amount</label>
                  <input class="form-control form-control-lg" type="text" id="vat_amount" name="vat_amount" value="300.00">
              </div>
              <h5 class="card-title mb-3 mt-4">Options</h5>
              <div class="ms-4 mb-3">
                  <!--
                  <div class="mb-3">
                      <input class="form-check-input" type="checkbox" id="qr_logo" name="qr_logo" {{ old('qr_logo') == 'on' ? 'checked' : '' }}>
                      <label class="form-check-label" for="qr_logo">Add an image in the center of the QR Code</label>
                  </div>
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="qr_options" id="option1" value="download" checked>
                      <label class="form-check-label" for="option1">
                          Download QR Code image
                      </label>
                  </div>
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="qr_options" id="option2" value="store">
                      <label class="form-check-label" for="option2">
                          Save QR Code image to server
                      </label>
                  </div> -->
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="qr_options" id="option3" value="pdf" checked>
                      <label class="form-check-label" for="option3">
                          Generate PDF with QR Code image
                      </label>
                  </div>
              </div>
              <button type="submit" class="btn btn-primary">Create QR Code</button>
            </form>
          </div>
        </div>
    </body>
</html>
