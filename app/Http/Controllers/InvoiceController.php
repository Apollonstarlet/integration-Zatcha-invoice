<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use chillerlan\QRCode\QRCode;
use App\Classes\LogoOptions;
use App\Classes\QRImageWithLogo;
use PDF;

class InvoiceController extends Controller
{
    private $logo;
    protected $temporary_image_file_name;
    protected $temporary_image_file_path;
    protected $temporary_pdf_file_name;
    protected $base64_image_string;
    protected $base64_image_string_without_header;

    public function __construct()
    {
        $this->logo = 'images/lion_head.png'; // https://www.silhouette.pics/83600/free-lion-head-tattoo-download.php
        $this->temporary_image_file_name = time() . '.png';
        $this->temporary_image_file_path = 'qr_images/' . $this->temporary_image_file_name;
        $this->temporary_pdf_file_name = time() . '.pdf';
    }

    public function to_base64_string(array $qr_data)
    {
        $generatedString = GenerateQrCode::fromArray($this->process($qr_data))->toBase64();

        return $generatedString;

        // > Output
        // AQVTYWxsYQIKMTIzNDU2Nzg5MQMUMjAyMS0wNy0xMlQxNDoyNTowOVoEBjEwMC4wMAUFMTUuMDA=
    }

    public function render(array $qr_data)
    {
        // data:image/png;base64, .........
        $displayQRCodeAsBase64 = GenerateQrCode::fromArray($this->process($qr_data))->render();

        return $displayQRCodeAsBase64;

        // now you can inject the output to src of html img tag :)
        // <img src="$displayQRCodeAsBase64" alt="QR Code" />
    }

    public function render_with_logo(array $qr_data, string $logo)
    {
        // Prepare ZATCA encrypted string
        $QRCodeDataAsBase64 = $this->to_base64_string($qr_data);

        $options = new LogoOptions;
        $options->version          = QRCode::VERSION_AUTO; // 10
        $options->eccLevel         = QRCode::ECC_H; //0b10;
        $options->imageBase64      = true;
        $options->logoSpaceWidth   = 20;
        $options->logoSpaceHeight  = 20;
        $options->scale            = 5;
        $options->imageTransparent = true;

        //header('Content-type: image/png');

        $qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($QRCodeDataAsBase64));

        // dump the output, with an additional logo
        return $qrOutputInterface->dump(null, $logo);
    }

    public function process(array $qr_data)
    {
        return [
            new Seller($qr_data['seller_name']), // seller name
            new TaxNumber($qr_data['vat_number']), // seller tax number
            new InvoiceDate($qr_data['invoice_date']), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($qr_data['total_amount']), // invoice total amount
            new InvoiceTaxAmount($qr_data['vat_amount']) // invoice tax amount
            // TODO :: Support others tags
        ];
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seller_name' => 'required',
            'vat_number' => 'required',
            'invoice_date' => 'required',
            'total_amount' => 'required',
            'vat_amount' => 'required',
            'qr_options' => 'required',
        ]);
        $qr_data = $validator->validated();
        $base64_image = "";
        return $qr_data;
        // if ($request->has('qr_logo')) {
        //     $this->base64_image_string = $this->render_with_logo($qr_data, $this->logo);
        // } else {
        //     $this->base64_image_string = $this->render($qr_data);
        // }

        // switch ($request->qr_options) {
        //     case "pdf":
        //         return $this->pdf_file_with_image();
        //         break;
        // }
    }

    public function pdf_file_with_image()
    {
        $data = [
            'title' => 'Invoice number: IN-123456789',
            'date' => date('m/d/Y'),
            'qr_image' => $this->image_html($this->base64_image_string),
        ];

        // First method
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->loadHTML('<h1>Test</h1>');
        // return $pdf->download();

        // Second method
        $pdf = PDF::loadView('pdf-with-qr', $data);

        return $pdf->download($this->temporary_pdf_file_name);
    }

    public function image_html($base64_file)
    {
        return '<img style="width: 200px;" src="' . $base64_file . '" alt="QR Code" />';
    }
}
