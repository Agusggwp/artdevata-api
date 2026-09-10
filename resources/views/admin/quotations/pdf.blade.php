<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Quotation {{ $quotation->quotation_number }}</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #1e293b;
      margin: 0;
      padding: 0;
      font-size: 12px;
      line-height: 1.5;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .brand-title {
      font-size: 24px;
      font-weight: bold;
      color: #14433B;
      letter-spacing: -0.5px;
    }
    .brand-sub {
      font-size: 10px;
      font-weight: bold;
      color: #0E5D55;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .quotation-badge {
      font-size: 16px;
      font-weight: bold;
      color: #14433B;
      text-align: right;
    }
    .meta-text {
      font-size: 11px;
      color: #64748b;
      text-align: right;
    }
    .client-card {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 12px;
      margin-bottom: 20px;
    }
    .client-title {
      font-size: 10px;
      font-weight: bold;
      color: #14433B;
      text-transform: uppercase;
      margin-bottom: 4px;
    }
    .client-name {
      font-size: 13px;
      font-weight: bold;
      color: #0f172a;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .items-table th {
      background-color: #14433B;
      color: #ffffff;
      font-size: 10px;
      font-weight: bold;
      text-transform: uppercase;
      padding: 8px 10px;
      text-align: left;
    }
    .items-table td {
      padding: 8px 10px;
      border-bottom: 1px solid #e2e8f0;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .totals-table {
      width: 40%;
      float: right;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .totals-table td {
      padding: 6px 10px;
    }
    .grand-total {
      font-size: 14px;
      font-weight: bold;
      color: #14433B;
      border-top: 2px solid #14433B;
    }
    .clear { clear: both; }
    .terms-box {
      background-color: #f8fafc;
      border-left: 4px solid #14433B;
      padding: 10px;
      font-size: 10px;
      color: #334155;
      margin-top: 30px;
    }
    .footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 9px;
      color: #94a3b8;
      border-top: 1px solid #e2e8f0;
      padding-top: 8px;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <table class="header-table">
    <tr>
      <td>
        <div class="brand-title">ARTDEVATA</div>
        <div class="brand-sub">IT Solusi & Software Development</div>
        <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Website: artdevata.net | Email: admin@artdevata.net</div>
      </td>
      <td class="text-right">
        <div class="quotation-badge">QUOTATION</div>
        <div class="meta-text">No: <strong>{{ $quotation->quotation_number }}</strong></div>
        <div class="meta-text">Tanggal: {{ $quotation->issue_date->format('d/m/Y') }}</div>
        <div class="meta-text">Berlaku s/d: {{ $quotation->valid_until->format('d/m/Y') }}</div>
      </td>
    </tr>
  </table>

  <!-- Client Info -->
  <div class="client-card">
    <div class="client-title">Penawaran Ditujukan Kepada:</div>
    <div class="client-name">{{ $quotation->client?->name }}</div>
    @if($quotation->client?->company_name)
      <div style="font-weight: bold; color: #475569;">{{ $quotation->client->company_name }}</div>
    @endif
    <div>{{ $quotation->client?->email ?? '-' }} | {{ $quotation->client?->phone ?? '-' }}</div>
    <div>{{ $quotation->client?->address ?? '-' }}</div>
  </div>

  <!-- Items Table -->
  <table class="items-table">
    <thead>
      <tr>
        <th class="text-center" style="width: 30px;">No</th>
        <th>Deskripsi Pekerjaan / Layanan</th>
        <th class="text-center" style="width: 60px;">Qty</th>
        <th class="text-right" style="width: 100px;">Harga Satuan</th>
        <th class="text-right" style="width: 80px;">Diskon</th>
        <th class="text-right" style="width: 110px;">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quotation->items as $index => $item)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td>
            <strong>{{ $item->description }}</strong>
          </td>
          <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
          <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
          <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Totals -->
  <table class="totals-table">
    <tr>
      <td>Subtotal:</td>
      <td class="text-right">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Diskon Tambahan:</td>
      <td class="text-right">- Rp {{ number_format($quotation->discount, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Pajak (PPN):</td>
      <td class="text-right">+ Rp {{ number_format($quotation->tax, 0, ',', '.') }}</td>
    </tr>
    <tr class="grand-total">
      <td>Grand Total:</td>
      <td class="text-right">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
    </tr>
  </table>

  <div class="clear"></div>

  <!-- Terms & Notes -->
  @if($quotation->terms || $quotation->notes)
    <div class="terms-box">
      @if($quotation->terms)
        <strong>Syarat & Ketentuan:</strong>
        <div style="white-space: pre-line; margin-bottom: 6px;">{{ $quotation->terms }}</div>
      @endif
      @if($quotation->notes)
        <strong>Catatan Tambahan:</strong>
        <div style="white-space: pre-line;">{{ $quotation->notes }}</div>
      @endif
    </div>
  @endif

  <!-- Footer -->
  <div class="footer">
    ARTDEVATA IT Solusi • Dokumen Resmi Penawaran Harga • Diterbitkan Secara Elektronik
  </div>

</body>
</html>
