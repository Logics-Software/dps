VERSION 5.00
Begin VB.Form frmPerubahanHarga 
   Caption         =   "Data Perubahan Harga"
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
      TabIndex        =   16
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdEdit 
      Caption         =   "Edit"
      Height          =   375
      Left            =   9600
      TabIndex        =   15
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdNew 
      Caption         =   "Baru"
      Height          =   375
      Left            =   8400
      TabIndex        =   14
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Simpan"
      Height          =   375
      Left            =   7200
      TabIndex        =   13
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdCancel 
      Caption         =   "Batal"
      Height          =   375
      Left            =   6000
      TabIndex        =   12
      Top             =   6720
      Width           =   1095
   End
   Begin VB.CommandButton cmdRefresh 
      Caption         =   "Refresh"
      Height          =   375
      Left            =   4800
      TabIndex        =   11
      Top             =   6720
      Width           =   1095
   End
   Begin VB.TextBox txtDiscountBaru 
      Height          =   285
      Left            =   2400
      TabIndex        =   10
      Top             =   6240
      Width           =   2415
   End
   Begin VB.TextBox txtHargaBaru 
      Height          =   285
      Left            =   2400
      TabIndex        =   9
      Top             =   5880
      Width           =   2415
   End
   Begin VB.TextBox txtDiscountLama 
      Height          =   285
      Left            =   2400
      TabIndex        =   8
      Top             =   5520
      Width           =   2415
   End
   Begin VB.TextBox txtHargaLama 
      Height          =   285
      Left            =   2400
      TabIndex        =   7
      Top             =   5160
      Width           =   2415
   End
   Begin VB.TextBox txtKodeBarang 
      Height          =   285
      Left            =   2400
      TabIndex        =   6
      Top             =   4800
      Width           =   2415
   End
   Begin VB.TextBox txtKeterangan 
      Height          =   285
      Left            =   2400
      TabIndex        =   5
      Top             =   4440
      Width           =   2415
   End
   Begin VB.TextBox txtTanggalPerubahan 
      Height          =   285
      Left            =   2400
      TabIndex        =   4
      Top             =   4080
      Width           =   2415
   End
   Begin VB.TextBox txtNoPerubahan 
      Height          =   285
      Left            =   2400
      TabIndex        =   3
      Top             =   3720
      Width           =   2415
   End
   Begin VB.ListBox lstData 
      Height          =   3375
      Left            =   120
      TabIndex        =   2
      Top             =   240
      Width           =   11775
   End
   Begin VB.Label lblDiscountBaru 
      Caption         =   "Discount Baru:"
      Height          =   255
      Left            =   120
      TabIndex        =   21
      Top             =   6240
      Width           =   2175
   End
   Begin VB.Label lblHargaBaru 
      Caption         =   "Harga Baru:"
      Height          =   255
      Left            =   120
      TabIndex        =   20
      Top             =   5880
      Width           =   2175
   End
   Begin VB.Label lblDiscountLama 
      Caption         =   "Discount Lama:"
      Height          =   255
      Left            =   120
      TabIndex        =   19
      Top             =   5520
      Width           =   2175
   End
   Begin VB.Label lblHargaLama 
      Caption         =   "Harga Lama:"
      Height          =   255
      Left            =   120
      TabIndex        =   18
      Top             =   5160
      Width           =   2175
   End
   Begin VB.Label lblKodeBarang 
      Caption         =   "Kode Barang:"
      Height          =   255
      Left            =   120
      TabIndex        =   17
      Top             =   4800
      Width           =   2175
   End
   Begin VB.Label lblKeterangan 
      Caption         =   "Keterangan:"
      Height          =   255
      Left            =   120
      TabIndex        =   22
      Top             =   4440
      Width           =   2175
   End
   Begin VB.Label lblTanggalPerubahan 
      Caption         =   "Tanggal Perubahan:"
      Height          =   255
      Left            =   120
      TabIndex        =   23
      Top             =   4080
      Width           =   2175
   End
   Begin VB.Label lblNoPerubahan 
      Caption         =   "No. Perubahan:"
      Height          =   255
      Left            =   120
      TabIndex        =   24
      Top             =   3720
      Width           =   2175
   End
End
Attribute VB_Name = "frmPerubahanHarga"
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
    txtNoPerubahan.SetFocus
End Sub

Private Sub cmdSave_Click()
    Dim response As String
    Dim noperubahan As String
    Dim tanggalperubahan As String
    Dim keterangan As String
    Dim kodebarang As String
    Dim hargalama As Double
    Dim discountlama As Double
    Dim hargabaru As Double
    Dim discountbaru As Double
    
    ' Validasi
    If Trim(txtNoPerubahan.Text) = "" Then
        MsgBox "No. Perubahan harus diisi!", vbExclamation
        txtNoPerubahan.SetFocus
        Exit Sub
    End If
    
    If Trim(txtTanggalPerubahan.Text) = "" Then
        MsgBox "Tanggal Perubahan harus diisi!", vbExclamation
        txtTanggalPerubahan.SetFocus
        Exit Sub
    End If
    
    If Trim(txtKeterangan.Text) = "" Then
        MsgBox "Keterangan harus diisi!", vbExclamation
        txtKeterangan.SetFocus
        Exit Sub
    End If
    
    If Trim(txtKodeBarang.Text) = "" Then
        MsgBox "Kode Barang harus diisi!", vbExclamation
        txtKodeBarang.SetFocus
        Exit Sub
    End If
    
    ' Ambil data dari form
    noperubahan = Trim(txtNoPerubahan.Text)
    tanggalperubahan = Trim(txtTanggalPerubahan.Text)
    keterangan = Trim(txtKeterangan.Text)
    kodebarang = Trim(txtKodeBarang.Text)
    hargalama = Val(txtHargaLama.Text)
    discountlama = Val(txtDiscountLama.Text)
    hargabaru = Val(txtHargaBaru.Text)
    discountbaru = Val(txtDiscountBaru.Text)
    
    ' Simpan data
    If isEditMode And currentId > 0 Then
        response = UpdatePerubahanharga(currentId, noperubahan, tanggalperubahan, _
            keterangan, kodebarang, hargalama, discountlama, hargabaru, discountbaru)
    Else
        response = CreatePerubahanharga(noperubahan, tanggalperubahan, _
            keterangan, kodebarang, hargalama, discountlama, hargabaru, discountbaru)
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
    ' Parse data dari list (format: "ID | No Perubahan | Tanggal | Keterangan | Kode Barang")
    ' Untuk implementasi lengkap, perlu parse JSON response
    ' Contoh sederhana:
    isEditMode = True
    ' currentId = ParseIdFromList(itemData)
    ' LoadDataToForm(currentId)
    
    MsgBox "Fitur edit memerlukan parsing JSON response. Gunakan fungsi GetPerubahanhargaById untuk load data.", vbInformation
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
    ' response = DeletePerubahanhargaById(id)
    
    MsgBox "Fitur delete memerlukan parsing JSON response untuk mendapatkan ID.", vbInformation
End Sub

Private Sub cmdRefresh_Click()
    LoadData
End Sub

Private Sub LoadData()
    Dim response As String
    Dim i As Long
    Dim jsonData As String
    
    lstData.Clear
    
    response = GetAllPerubahanharga(1, 100)
    
    ' Parse JSON response (sederhana)
    ' Untuk implementasi lengkap, gunakan JSON parser library
    ' Contoh sederhana menampilkan response:
    If InStr(response, """success"": true") > 0 Then
        ' Parse dan tampilkan data
        ' lstData.AddItem "ID | No Perubahan | Tanggal | Keterangan | Kode Barang | Harga Lama | Harga Baru"
        ' Loop through data array and add to list
    Else
        lstData.AddItem "Error loading data: " & response
    End If
End Sub

Private Sub ClearForm()
    txtNoPerubahan.Text = ""
    txtTanggalPerubahan.Text = Format(Date, "yyyy-mm-dd")
    txtKeterangan.Text = ""
    txtKodeBarang.Text = ""
    txtHargaLama.Text = "0"
    txtDiscountLama.Text = "0"
    txtHargaBaru.Text = "0"
    txtDiscountBaru.Text = "0"
End Sub

Private Sub lstData_Click()
    ' Load selected data to form for editing
    ' Implementasi parsing JSON response
End Sub

