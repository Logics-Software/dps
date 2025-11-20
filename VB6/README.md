# VB6 Integration untuk Pembelianbarang dan Perubahanharga

## File yang Dibutuhkan

1. **ModuleAPI.bas** - Module untuk API calls ke PHP backend
2. **frmPembelianBarang.frm** - Form untuk CRUD Pembelian Barang
3. **frmPerubahanHarga.frm** - Form untuk CRUD Perubahan Harga

## Setup

### 1. Konfigurasi API URL

Edit `ModuleAPI.bas` dan sesuaikan `API_BASE_URL`:
```vb
Public Const API_BASE_URL As String = "http://localhost:8000/api/"
```

### 2. Reference yang Diperlukan

Pastikan project VB6 memiliki reference ke:
- Microsoft XML, v6.0 (untuk XMLHTTP)

### 3. Import File ke VB6

1. Buka VB6 IDE
2. File > Add Project atau buat project baru
3. Project > Add Module, pilih `ModuleAPI.bas`
4. Project > Add Form, pilih `frmPembelianBarang.frm` dan `frmPerubahanHarga.frm`

## Penggunaan

### Pembelian Barang

1. Jalankan form `frmPembelianBarang`
2. Klik "Baru" untuk input data baru
3. Isi semua field yang diperlukan
4. Klik "Simpan" untuk menyimpan data
5. Klik "Refresh" untuk reload data dari server

### Perubahan Harga

1. Jalankan form `frmPerubahanHarga`
2. Klik "Baru" untuk input data baru
3. Isi semua field yang diperlukan
4. Klik "Simpan" untuk menyimpan data
5. Klik "Refresh" untuk reload data dari server

## Catatan Penting

### JSON Parsing

Code yang disediakan adalah struktur dasar. Untuk implementasi lengkap, Anda perlu:

1. **JSON Parser Library** - Install library JSON parser untuk VB6 seperti:
   - VBA-JSON (https://github.com/VBA-tools/VBA-JSON)
   - atau library JSON parser lainnya

2. **Parse Response** - Update fungsi `LoadData()` di kedua form untuk parse JSON response dan tampilkan di ListBox

### Contoh Parsing JSON (dengan VBA-JSON)

```vb
' Di ModuleAPI.bas, tambahkan reference ke JSON parser
' Kemudian update LoadData():

Private Sub LoadData()
    Dim response As String
    Dim json As Object
    Dim data As Object
    Dim i As Long
    
    response = GetAllPembelianbarang(1, 100)
    Set json = JsonConverter.ParseJson(response)
    
    If json("success") = True Then
        lstData.Clear
        For i = 1 To json("data").Count
            Set data = json("data")(i)
            lstData.AddItem data("id") & " | " & data("nopembelian") & " | " & _
                data("tanggalpembelian") & " | " & data("namasupplier")
        Next i
    End If
End Sub
```

### Error Handling

Code sudah termasuk basic error handling. Untuk production, tambahkan:
- Try-catch yang lebih detail
- Logging error
- Retry mechanism untuk network errors

### Date Format

Pastikan format tanggal sesuai dengan yang diharapkan API (yyyy-mm-dd).

## API Endpoints yang Tersedia

### Pembelianbarang
- `GET /api/pembelianbarang` - Get all
- `GET /api/pembelianbarang?id={id}` - Get by ID
- `GET /api/pembelianbarang?nopembelian={no}` - Get by nopembelian
- `GET /api/pembelianbarang?nopembelian={no}&kodebarang={kode}` - Get by both
- `POST /api/pembelianbarang` - Create
- `PUT /api/pembelianbarang` - Update
- `DELETE /api/pembelianbarang?id={id}` - Delete by ID
- `DELETE /api/pembelianbarang?nopembelian={no}` - Delete by nopembelian

### Perubahanharga
- `GET /api/perubahanharga` - Get all
- `GET /api/perubahanharga?id={id}` - Get by ID
- `GET /api/perubahanharga?noperubahan={no}` - Get by noperubahan
- `GET /api/perubahanharga?noperubahan={no}&kodebarang={kode}` - Get by both
- `POST /api/perubahanharga` - Create
- `PUT /api/perubahanharga` - Update
- `DELETE /api/perubahanharga?id={id}` - Delete by ID
- `DELETE /api/perubahanharga?noperubahan={no}` - Delete by noperubahan

## Troubleshooting

1. **Error: Object required** - Pastikan reference ke MSXML2 sudah ditambahkan
2. **Connection timeout** - Cek URL API dan pastikan server PHP berjalan
3. **JSON parse error** - Install JSON parser library atau gunakan manual parsing
4. **CORS error** - Pastikan server PHP mengizinkan CORS dari aplikasi VB6

