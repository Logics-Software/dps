VERSION 5.00
Begin VB.Form frmMasterSupplier 
   Caption         =   "Master Supplier Management"
   ClientHeight    =   6360
   ClientLeft      =   4950
   ClientTop       =   2475
   ClientWidth     =   9450
   LinkTopic       =   "Form1"
   ScaleHeight     =   6360
   ScaleWidth      =   9450
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   7560
      TabIndex        =   12
      Top             =   3960
      Width           =   1215
   End
   Begin VB.CommandButton cmdUpdate 
      Caption         =   "Update"
      Height          =   375
      Left            =   6240
      TabIndex        =   11
      Top             =   3960
      Width           =   1215
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Save"
      Height          =   375
      Left            =   4920
      TabIndex        =   10
      Top             =   3960
      Width           =   1215
   End
   Begin VB.CommandButton cmdLoad 
      Caption         =   "Load Data"
      Height          =   375
      Left            =   3600
      TabIndex        =   9
      Top             =   3960
      Width           =   1215
   End
   Begin VB.ComboBox cmbStatus 
      Height          =   315
      Left            =   2760
      Style           =   2  'Dropdown List
      TabIndex        =   8
      Top             =   3360
      Width           =   2175
   End
   Begin VB.TextBox txtKontakPerson 
      Height          =   315
      Left            =   2760
      TabIndex        =   7
      Top             =   3000
      Width           =   2175
   End
   Begin VB.TextBox txtNoTelepon 
      Height          =   315
      Left            =   2760
      TabIndex        =   6
      Top             =   2640
      Width           =   2175
   End
   Begin VB.TextBox txtAlamatSupplier 
      Height          =   555
      Left            =   2760
      MultiLine       =   -1  'True
      TabIndex        =   5
      Top             =   2040
      Width           =   2175
   End
   Begin VB.TextBox txtNamaSupplier 
      Height          =   315
      Left            =   2760
      TabIndex        =   4
      Top             =   1680
      Width           =   2175
   End
   Begin VB.TextBox txtKodeSupplier 
      Height          =   315
      Left            =   2760
      TabIndex        =   3
      Top             =   1320
      Width           =   2175
   End
   Begin VB.TextBox txtID 
      Height          =   315
      Left            =   2760
      TabIndex        =   2
      Top             =   960
      Width           =   1215
   End
   Begin VB.ListBox lstData 
      Height          =   2730
      Left            =   5400
      TabIndex        =   1
      Top             =   960
      Width           =   3855
   End
   Begin VB.Label lblStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   960
      TabIndex        =   17
      Top             =   3360
      Width           =   1695
   End
   Begin VB.Label lblKontakPerson 
      Caption         =   "Kontak Person:"
      Height          =   255
      Left            =   960
      TabIndex        =   16
      Top             =   3000
      Width           =   1695
   End
   Begin VB.Label lblNoTelepon 
      Caption         =   "No Telepon:"
      Height          =   255
      Left            =   960
      TabIndex        =   15
      Top             =   2640
      Width           =   1695
   End
   Begin VB.Label lblAlamatSupplier 
      Caption         =   "Alamat Supplier:"
      Height          =   255
      Left            =   960
      TabIndex        =   14
      Top             =   2040
      Width           =   1695
   End
   Begin VB.Label lblNamaSupplier 
      Caption         =   "Nama Supplier:"
      Height          =   255
      Left            =   960
      TabIndex        =   13
      Top             =   1680
      Width           =   1695
   End
   Begin VB.Label lblKodeSupplier 
      Caption         =   "Kode Supplier:"
      Height          =   255
      Left            =   960
      TabIndex        =   18
      Top             =   1320
      Width           =   1695
   End
   Begin VB.Label lblID 
      Caption         =   "ID:"
      Height          =   255
      Left            =   960
      TabIndex        =   19
      Top             =   960
      Width           =   1695
   End
   Begin VB.Label lblInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   960
      TabIndex        =   0
      Top             =   4560
      Width           =   8175
   End
End
Attribute VB_Name = "frmMasterSupplier"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    cmbStatus.AddItem "aktif"
    cmbStatus.AddItem "non aktif"
    cmbStatus.ListIndex = 0
    
    Call cmdLoad_Click
End Sub

Private Sub cmdLoad_Click()
    Dim response As String
    Dim success As String
    Dim data As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim kode As String
    Dim nama As String
    
    On Error GoTo ErrorHandler
    
    lblInfo.Caption = "Loading data..."
    lstData.Clear
    
    response = GetAllMastersupplier(1, 100)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    success = ParseJSONValue(response, "success")
    If LCase(success) = "true" Then
        data = ParseJSONValue(response, "data")
        If Left(data, 1) = "[" Then
            startPos = InStr(data, "{")
            Do While startPos > 0
                endPos = InStr(startPos, data, "}")
                If endPos = 0 Then Exit Do
                item = Mid(data, startPos, endPos - startPos + 1)
                
                kode = ParseJSONValue(item, "kodesupplier")
                nama = ParseJSONValue(item, "namasupplier")
                
                If kode <> "" Then
                    lstData.AddItem kode & " - " & nama
                End If
                
                startPos = InStr(endPos, data, "{")
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
    Dim kodesupplier As String
    Dim namasupplier As String
    Dim alamatsupplier As String
    Dim notelepon As String
    Dim kontakperson As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    kodesupplier = Trim(txtKodeSupplier.Text)
    namasupplier = Trim(txtNamaSupplier.Text)
    alamatsupplier = Trim(txtAlamatSupplier.Text)
    notelepon = Trim(txtNoTelepon.Text)
    kontakperson = Trim(txtKontakPerson.Text)
    status = cmbStatus.Text
    
    If kodesupplier = "" Or namasupplier = "" Then
        MsgBox "Kode Supplier dan Nama Supplier harus diisi!", vbExclamation
        Exit Sub
    End If
    
    lblInfo.Caption = "Saving data..."
    response = CreateMastersupplier(kodesupplier, namasupplier, alamatsupplier, notelepon, kontakperson, status)
    
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
    Dim kodesupplier As String
    Dim namasupplier As String
    Dim alamatsupplier As String
    Dim notelepon As String
    Dim kontakperson As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    If Trim(txtID.Text) = "" Then
        MsgBox "ID harus diisi untuk update!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.Text)
    kodesupplier = Trim(txtKodeSupplier.Text)
    namasupplier = Trim(txtNamaSupplier.Text)
    alamatsupplier = Trim(txtAlamatSupplier.Text)
    notelepon = Trim(txtNoTelepon.Text)
    kontakperson = Trim(txtKontakPerson.Text)
    status = cmbStatus.Text
    
    lblInfo.Caption = "Updating data..."
    response = UpdateMastersupplier(id, kodesupplier, namasupplier, alamatsupplier, notelepon, kontakperson, status)
    
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
    Dim confirmResult As VbMsgBoxResult
    
    On Error GoTo ErrorHandler
    
    If Trim(txtID.Text) = "" Then
        MsgBox "ID harus diisi untuk delete!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.Text)
    confirmResult = MsgBox("Yakin ingin menghapus data dengan ID " & id & "?", vbYesNo + vbQuestion, "Konfirmasi")
    If confirmResult = vbNo Then Exit Sub
    
    lblInfo.Caption = "Deleting data..."
    response = DeleteMastersupplierById(id)
    
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
    Dim kodesupplier As String
    Dim response As String
    Dim data As String
    Dim statusValue As String
    
    On Error GoTo ErrorHandler
    
    If lstData.ListIndex < 0 Then Exit Sub
    
    selectedText = lstData.List(lstData.ListIndex)
    If InStr(selectedText, " - ") = 0 Then Exit Sub
    kodesupplier = Trim(Left(selectedText, InStr(selectedText, " - ") - 1))
    
    response = GetMastersupplierByKode(kodesupplier)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        data = ParseJSONValue(response, "data")
        txtID.Text = ParseJSONValue(data, "id")
        txtKodeSupplier.Text = ParseJSONValue(data, "kodesupplier")
        txtNamaSupplier.Text = ParseJSONValue(data, "namasupplier")
        txtAlamatSupplier.Text = ParseJSONValue(data, "alamatsupplier")
        txtNoTelepon.Text = ParseJSONValue(data, "notelepon")
        txtKontakPerson.Text = ParseJSONValue(data, "kontakperson")
        
        statusValue = ParseJSONValue(data, "status")
        If statusValue = "aktif" Then
            cmbStatus.ListIndex = 0
        Else
            cmbStatus.ListIndex = 1
        End If
        
        lblInfo.Caption = "Data loaded: " & kodesupplier
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub ClearForm()
    txtID.Text = ""
    txtKodeSupplier.Text = ""
    txtNamaSupplier.Text = ""
    txtAlamatSupplier.Text = ""
    txtNoTelepon.Text = ""
    txtKontakPerson.Text = ""
    cmbStatus.ListIndex = 0
End Sub


