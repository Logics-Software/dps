VERSION 5.00
Begin VB.Form frmPembelianBarang 
   Caption         =   "Data Pembelian Barang"
   ClientHeight    =   7200
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   12000
   LinkTopic       =   "Form1"
   ScaleHeight     =   7200
   ScaleWidth      =   12000
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Hapus"
      Height          =   375
      Left            =   10800
      TabIndex        =   15
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdEdit 
      Caption         =   "Edit"
      Height          =   375
      Left            =   9600
      TabIndex        =   14
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdNew 
      Caption         =   "Baru"
      Height          =   375
      Left            =   8400
      TabIndex        =   13
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Simpan"
      Height          =   375
      Left            =   7200
      TabIndex        =   12
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdCancel 
      Caption         =   "Batal"
      Height          =   375
      Left            =   6000
      TabIndex        =   11
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdRefresh 
      Caption         =   "Refresh"
      Height          =   375
      Left            =   4800
      TabIndex        =   10
      Top             =   6720
      Width           =   1095
   End
   Begin VB.TextBox txtTotalHarga 
      Height          =   285
      Left            =   2400
      TabIndex        =   9
      Top             =   6240
      Width           =   2415
   End
   Begin VB.TextBox txtDiscount 
      Height          =   285
      Left            =   2400
      TabIndex        =   8
      Top             =   5880
      Width           =   2415
   End
   Begin VB.TextBox txtHarga 
      Height          =   285
      Left            =   2400
      TabIndex        =   7
      Top             =   5520
      Width           =   2415
   End
   Begin VB.TextBox txtJumlah 
      Height          =   285
      Left            =   2400
      TabIndex        =   6
      Top             =   5160
      Width           =   2415
   End
   Begin VB.TextBox txtKodeBarang 
      Height          =   285
      Left            =   2400
      TabIndex        =   5
      Top             =   4800
      Width           =   2415
   End
   Begin VB.TextBox txtNamaSupplier 
      Height          =   285
      Left            =   2400
      TabIndex        =   4
      Top             =   4440
      Width           =   2415
   End
   Begin VB.TextBox txtTanggalPembelian 
      Height          =   285
      Left            =   2400
      TabIndex        =   3
      Top             =   4080
      Width           =   2415
   End
   Begin VB.TextBox txtNoPembelian 
      Height          =   285
      Left            =   2400
      TabIndex        =   2
      Top             =   3720
      Width           =   2415
   End
   Begin VB.ListBox lstData 
      Height          =   3375
      Left            =   120
      TabIndex        =   1
      Top             =   240
      Width           =   11775
   End
   Begin VB.Label lblTotalHarga 
      Caption         =   "Total Harga:"
      Height          =   255
      Left            =   120
      TabIndex        =   20
      Top             =   6240
      Width           =   2175
   End
   Begin VB.Label lblDiscount 
      Caption         =   "Discount:"
      Height          =   255
      Left            =   120
      TabIndex        =   19
      Top             =   5880
      Width           =   2175
   End
   Begin VB.Label lblHarga 
      Caption         =   "Harga:"
      Height          =   255
      Left            =   120
      TabIndex        =   18
      Top             =   5520
      Width           =   2175
   End
   Begin VB.Label lblJumlah 
      Caption         =   "No. Pembelian:"
      Height          =   255
      Left            =   120
      TabIndex        =   17
      Top             =   5160
      Width           =   2175
   End
   Begin VB.Label lblKodeBarang 
      Caption         =   "Kode Barang:"
      Height          =   255
      Left            =   120
      TabIndex        =   16
      Top             =   4800
      Width           =   2175
   End
   Begin VB.Label lblNamaSupplier 
      Caption         =   "Nama Supplier:"
      Height          =   255
      Left            =   120
      TabIndex        =   21
      Top             =   4440
      Width           =   2175
   End
   Begin VB.Label lblTanggalPembelian 
      Caption         =   "Tanggal Pembelian:"
      Height          =   255
      Left            =   120
      TabIndex        =   22
      Top             =   4080
      Width           =   2175
   End
   Begin VB.Label lblNoPembelian 
      Caption         =   "No. Pembelian:"
      Height          =   255
      Left            =   120
      TabIndex        =   23
      Top             =   3720
      Width           =   2175
   End
End
Attribute VB_Name = "frmPembelianBarang"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Dim currentId As Long
Dim isEditMode As Boolean

Private Sub Form_Load()
    isEditMode = False
    currentId = 0
    ClearForm
    LoadData
End Sub

Private Sub cmdNew_Click()
    isEditMode = False
    currentId = 0
    ClearForm
    txtNoPembelian.SetFocus
End Sub

Private Sub cmdSave_Click()
    Dim response As String
    Dim nopembelian As String
    Dim tanggalpembelian As String
    Dim namasupplier As String
    Dim kodebarang As String
    Dim jumlah As Double
    Dim harga As Double
    Dim discount As Double
    Dim totalharga As Double
    
    ' Validasi
    If Trim(txtNoPembelian.Text) = "" Then
        MsgBox "No. Pembelian harus diisi!", vbExclamation
        txtNoPembelian.SetFocus
        Exit Sub
    End If
    
    If Trim(txtTanggalPembelian.Text) = "" Then
        MsgBox "Tanggal Pembelian harus diisi!", vbExclamation
        txtTanggalPembelian.SetFocus
        Exit Sub
    End If
    
    If Trim(txtNamaSupplier.Text) = "" Then
        MsgBox "Nama Supplier harus diisi!", vbExclamation
        txtNamaSupplier.SetFocus
        Exit Sub
    End If
    
    If Trim(txtKodeBarang.Text) = "" Then
        MsgBox "Kode Barang harus diisi!", vbExclamation
        txtKodeBarang.SetFocus
        Exit Sub
    End If
    
    ' Ambil data dari form
    nopembelian = Trim(txtNoPembelian.Text)
    tanggalpembelian = Trim(txtTanggalPembelian.Text)
    namasupplier = Trim(txtNamaSupplier.Text)
    kodebarang = Trim(txtKodeBarang.Text)
    jumlah = Val(txtJumlah.Text)
    harga = Val(txtHarga.Text)
    discount = Val(txtDiscount.Text)
    totalharga = Val(txtTotalHarga.Text)
    
    ' Hitung total harga jika belum diisi
    If totalharga = 0 Then
        totalharga = (jumlah * harga) - discount
        txtTotalHarga.Text = Format(totalharga, "0.00")
    End If
    
    ' Simpan data
    If isEditMode And currentId > 0 Then
        response = UpdatePembelianbarang(currentId, nopembelian, tanggalpembelian, _
            namasupplier, kodebarang, jumlah, harga, discount, totalharga)
    Else
        response = CreatePembelianbarang(nopembelian, tanggalpembelian, _
            namasupplier, kodebarang, jumlah, harga, discount, totalharga)
    End If
    
    ' Cek response
    If InStr(response, """success"": true") > 0 Then
        MsgBox "Data berhasil disimpan!", vbInformation
        ClearForm
        LoadData
    Else
        MsgBox "Error: " & response, vbCritical
    End If
End Sub

Private Sub cmdCancel_Click()
    ClearForm
    isEditMode = False
    currentId = 0
End Sub

Private Sub cmdEdit_Click()
    Dim selectedIndex As Long
    Dim itemData As String
    
    selectedIndex = lstData.ListIndex
    If selectedIndex < 0 Then
        MsgBox "Pilih data yang akan diedit!", vbExclamation
        Exit Sub
    End If
    
    itemData = lstData.List(selectedIndex)
    ' Parse data dari list (format: "ID | No Pembelian | Tanggal | Supplier | Kode Barang")
    ' Untuk implementasi lengkap, perlu parse JSON response
    ' Contoh sederhana:
    isEditMode = True
    ' currentId = ParseIdFromList(itemData)
    ' LoadDataToForm(currentId)
    
    MsgBox "Fitur edit memerlukan parsing JSON response. Gunakan fungsi GetPembelianbarangById untuk load data.", vbInformation
End Sub

Private Sub cmdDelete_Click()
    Dim selectedIndex As Long
    Dim response As String
    Dim confirm As VbMsgBoxResult
    
    selectedIndex = lstData.ListIndex
    If selectedIndex < 0 Then
        MsgBox "Pilih data yang akan dihapus!", vbExclamation
        Exit Sub
    End If
    
    confirm = MsgBox("Yakin akan menghapus data ini?", vbYesNo + vbQuestion)
    If confirm = vbNo Then Exit Sub
    
    ' Untuk implementasi lengkap, perlu ambil ID dari selected item
    ' response = DeletePembelianbarangById(id)
    
    MsgBox "Fitur delete memerlukan parsing JSON response untuk mendapatkan ID.", vbInformation
End Sub

Private Sub cmdRefresh_Click()
    LoadData
End Sub

Private Sub txtJumlah_Change()
    CalculateTotal
End Sub

Private Sub txtHarga_Change()
    CalculateTotal
End Sub

Private Sub txtDiscount_Change()
    CalculateTotal
End Sub

Private Sub CalculateTotal()
    Dim jumlah As Double
    Dim harga As Double
    Dim discount As Double
    Dim total As Double
    
    jumlah = Val(txtJumlah.Text)
    harga = Val(txtHarga.Text)
    discount = Val(txtDiscount.Text)
    
    total = (jumlah * harga) - discount
    If total < 0 Then total = 0
    
    txtTotalHarga.Text = Format(total, "0.00")
End Sub

Private Sub LoadData()
    Dim response As String
    Dim i As Long
    Dim jsonData As String
    
    lstData.Clear
    
    response = GetAllPembelianbarang(1, 100)
    
    ' Parse JSON response (sederhana)
    ' Untuk implementasi lengkap, gunakan JSON parser library
    ' Contoh sederhana menampilkan response:
    If InStr(response, """success"": true") > 0 Then
        ' Parse dan tampilkan data
        ' lstData.AddItem "ID | No Pembelian | Tanggal | Supplier | Kode Barang | Jumlah | Harga | Total"
        ' Loop through data array and add to list
    Else
        lstData.AddItem "Error loading data: " & response
    End If
End Sub

Private Sub ClearForm()
    txtNoPembelian.Text = ""
    txtTanggalPembelian.Text = Format(Date, "yyyy-mm-dd")
    txtNamaSupplier.Text = ""
    txtKodeBarang.Text = ""
    txtJumlah.Text = "0"
    txtHarga.Text = "0"
    txtDiscount.Text = "0"
    txtTotalHarga.Text = "0"
End Sub

Private Sub lstData_Click()
    ' Load selected data to form for editing
    ' Implementasi parsing JSON response
End Sub

