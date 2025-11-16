VERSION 5.00
Begin VB.Form frmMastersales 
   Caption         =   "Mastersales Management"
   ClientHeight    =   6000
   ClientLeft      =   4950
   ClientTop       =   2475
   ClientWidth     =   9000
   LinkTopic       =   "Form1"
   ScaleHeight     =   6000
   ScaleWidth      =   9000
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   7200
      TabIndex        =   10
      Top             =   3600
      Width           =   1215
   End
   Begin VB.CommandButton cmdUpdate 
      Caption         =   "Update"
      Height          =   375
      Left            =   5880
      TabIndex        =   9
      Top             =   3600
      Width           =   1215
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Save"
      Height          =   375
      Left            =   4560
      TabIndex        =   8
      Top             =   3600
      Width           =   1215
   End
   Begin VB.CommandButton cmdLoad 
      Caption         =   "Load Data"
      Height          =   375
      Left            =   3240
      TabIndex        =   7
      Top             =   3600
      Width           =   1215
   End
   Begin VB.ComboBox cmbStatus 
      Height          =   315
      Left            =   2400
      Style           =   2  'Dropdown List
      TabIndex        =   6
      Top             =   3000
      Width           =   2175
   End
   Begin VB.TextBox txtNoTelepon 
      Height          =   315
      Left            =   2400
      TabIndex        =   5
      Top             =   2640
      Width           =   2175
   End
   Begin VB.TextBox txtAlamatSales 
      Height          =   315
      Left            =   2400
      TabIndex        =   4
      Top             =   2280
      Width           =   2175
   End
   Begin VB.TextBox txtNamaSales 
      Height          =   315
      Left            =   2400
      TabIndex        =   3
      Top             =   1920
      Width           =   2175
   End
   Begin VB.TextBox txtKodeSales 
      Height          =   315
      Left            =   2400
      TabIndex        =   2
      Top             =   1560
      Width           =   2175
   End
   Begin VB.TextBox txtID 
      Height          =   315
      Left            =   2400
      TabIndex        =   1
      Top             =   1200
      Width           =   1215
   End
   Begin VB.ListBox lstData 
      Height          =   2010
      Left            =   4800
      TabIndex        =   0
      Top             =   1200
      Width           =   3855
   End
   Begin VB.Label lblStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   600
      TabIndex        =   17
      Top             =   3000
      Width           =   1695
   End
   Begin VB.Label lblNoTelepon 
      Caption         =   "No Telepon:"
      Height          =   255
      Left            =   600
      TabIndex        =   16
      Top             =   2640
      Width           =   1695
   End
   Begin VB.Label lblAlamatSales 
      Caption         =   "Alamat Sales:"
      Height          =   255
      Left            =   600
      TabIndex        =   15
      Top             =   2280
      Width           =   1695
   End
   Begin VB.Label lblNamaSales 
      Caption         =   "Nama Sales:"
      Height          =   255
      Left            =   600
      TabIndex        =   14
      Top             =   1920
      Width           =   1695
   End
   Begin VB.Label lblKodeSales 
      Caption         =   "Kode Sales:"
      Height          =   255
      Left            =   600
      TabIndex        =   13
      Top             =   1560
      Width           =   1695
   End
   Begin VB.Label lblID 
      Caption         =   "ID:"
      Height          =   255
      Left            =   600
      TabIndex        =   12
      Top             =   1200
      Width           =   1695
   End
   Begin VB.Label lblInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   600
      TabIndex        =   11
      Top             =   4200
      Width           =   8175
   End
End
Attribute VB_Name = "frmMastersales"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    ' Initialize ComboBox
    cmbStatus.AddItem "aktif"
    cmbStatus.AddItem "non aktif"
    cmbStatus.ListIndex = 0
    
    ' Load data saat form dibuka
    Call cmdLoad_Click
End Sub

Private Sub cmdLoad_Click()
    Dim response As String
    Dim success As String
    Dim data As String
    Dim i As Long
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    
    On Error GoTo ErrorHandler
    
    lblInfo.Caption = "Loading data..."
    lstData.Clear
    
    ' Get all data
    response = GetAllMastersales(1, 100)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    success = ParseJSONValue(response, "success")
    
    If LCase(success) = "true" Then
        ' Parse data array (simplified - untuk production gunakan JSON parser yang lebih robust)
        data = ParseJSONValue(response, "data")
        
        ' Simple parsing untuk array (contoh sederhana)
        ' Format: [{"id":1,"kodesales":"SL001",...}, {...}]
        If Left(data, 1) = "[" Then
            startPos = 2 ' Skip [
            Do While startPos < Len(data)
                endPos = InStr(startPos, data, "}")
                If endPos = 0 Then Exit Do
                
                item = Mid(data, startPos, endPos - startPos + 1)
                ' Extract kodesales and namasales untuk display
                Dim kode As String
                Dim nama As String
                kode = ParseJSONValue(item, "kodesales")
                nama = ParseJSONValue(item, "namasales")
                
                If kode <> "" Then
                    lstData.AddItem kode & " - " & nama
                End If
                
                startPos = InStr(endPos, data, "{")
                If startPos = 0 Then Exit Do
            Loop
        End If
        
        lblInfo.Caption = "Data loaded successfully"
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
    
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub cmdSave_Click()
    Dim response As String
    Dim kodesales As String
    Dim namasales As String
    Dim alamatsales As String
    Dim notelepon As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    ' Validasi
    kodesales = Trim(txtKodeSales.text)
    namasales = Trim(txtNamaSales.text)
    
    If kodesales = "" Or namasales = "" Then
        MsgBox "Kode Sales dan Nama Sales harus diisi!", vbExclamation
        Exit Sub
    End If
    
    alamatsales = Trim(txtAlamatSales.text)
    notelepon = Trim(txtNoTelepon.text)
    status = cmbStatus.text
    
    lblInfo.Caption = "Saving data..."
    
    ' Create new data
    response = CreateMastersales(kodesales, namasales, alamatsales, notelepon, status)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil disimpan"
        MsgBox "Data berhasil disimpan!", vbInformation
        Call ClearForm
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
    
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdUpdate_Click()
    Dim response As String
    Dim id As Long
    Dim kodesales As String
    Dim namasales As String
    Dim alamatsales As String
    Dim notelepon As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    ' Validasi ID
    If Trim(txtID.text) = "" Then
        MsgBox "ID harus diisi untuk update!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.text)
    
    kodesales = Trim(txtKodeSales.text)
    namasales = Trim(txtNamaSales.text)
    alamatsales = Trim(txtAlamatSales.text)
    notelepon = Trim(txtNoTelepon.text)
    status = cmbStatus.text
    
    lblInfo.Caption = "Updating data..."
    
    ' Update data
    response = UpdateMastersales(id, kodesales, namasales, alamatsales, notelepon, status)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil diupdate"
        MsgBox "Data berhasil diupdate!", vbInformation
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
    
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdDelete_Click()
    Dim response As String
    Dim id As Long
    Dim confirm As VbMsgBoxResult
    
    On Error GoTo ErrorHandler
    
    ' Validasi ID
    If Trim(txtID.text) = "" Then
        MsgBox "ID harus diisi untuk delete!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.text)
    
    ' Konfirmasi
    confirm = MsgBox("Yakin ingin menghapus data dengan ID " & id & "?", vbYesNo + vbQuestion, "Konfirmasi")
    If confirm = vbNo Then Exit Sub
    
    lblInfo.Caption = "Deleting data..."
    
    ' Delete data
    response = DeleteMastersalesById(id)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil dihapus"
        MsgBox "Data berhasil dihapus!", vbInformation
        Call ClearForm
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
    
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub lstData_Click()
    Dim selectedText As String
    Dim kodesales As String
    Dim response As String
    Dim data As String
    Dim id As String
    
    On Error GoTo ErrorHandler
    
    If lstData.ListIndex < 0 Then Exit Sub
    
    selectedText = lstData.List(lstData.ListIndex)
    
    ' Extract kodesales dari list item (format: "SL001 - John Doe")
    kodesales = Trim(Left(selectedText, InStr(selectedText, " - ") - 1))
    
    If kodesales = "" Then Exit Sub
    
    ' Get data by kode sales
    response = GetMastersalesByKode(kodesales)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        data = ParseJSONValue(response, "data")
        
        ' Populate form fields
        ' CATATAN: JSON response sudah dalam format yang benar,
        ' tidak perlu URLDecode karena JSON sudah handle encoding.
        ' Jika ada masalah dengan karakter spesial, bisa gunakan:
        ' ParseJSONValue(data, "field", True) untuk decode
        id = ParseJSONValue(data, "id")
        txtID.text = id
        txtKodeSales.text = ParseJSONValue(data, "kodesales")
        txtNamaSales.text = ParseJSONValue(data, "namasales")
        txtAlamatSales.text = ParseJSONValue(data, "alamatsales")
        txtNoTelepon.text = ParseJSONValue(data, "notelepon")
        
        ' Set status
        Dim status As String
        status = ParseJSONValue(data, "status")
        If status = "aktif" Then
            cmbStatus.ListIndex = 0
        Else
            cmbStatus.ListIndex = 1
        End If
        
        lblInfo.Caption = "Data loaded: " & kodesales
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
    
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub ClearForm()
    txtID.text = ""
    txtKodeSales.text = ""
    txtNamaSales.text = ""
    txtAlamatSales.text = ""
    txtNoTelepon.text = ""
    cmbStatus.ListIndex = 0
End Sub

