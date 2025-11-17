VERSION 5.00
Begin VB.Form frmTabelGolongan 
   Caption         =   "Tabel Golongan Management"
   ClientHeight    =   5580
   ClientLeft      =   4950
   ClientTop       =   2475
   ClientWidth     =   9000
   LinkTopic       =   "Form1"
   ScaleHeight     =   5580
   ScaleWidth      =   9000
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   7200
      TabIndex        =   9
      Top             =   3240
      Width           =   1215
   End
   Begin VB.CommandButton cmdUpdate 
      Caption         =   "Update"
      Height          =   375
      Left            =   5880
      TabIndex        =   8
      Top             =   3240
      Width           =   1215
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Save"
      Height          =   375
      Left            =   4560
      TabIndex        =   7
      Top             =   3240
      Width           =   1215
   End
   Begin VB.CommandButton cmdLoad 
      Caption         =   "Load Data"
      Height          =   375
      Left            =   3240
      TabIndex        =   6
      Top             =   3240
      Width           =   1215
   End
   Begin VB.ComboBox cmbStatus 
      Height          =   315
      Left            =   2400
      Style           =   2  'Dropdown List
      TabIndex        =   5
      Top             =   2640
      Width           =   2175
   End
   Begin VB.TextBox txtNamaGolongan 
      Height          =   315
      Left            =   2400
      TabIndex        =   4
      Top             =   2280
      Width           =   2175
   End
   Begin VB.TextBox txtKodeGolongan 
      Height          =   315
      Left            =   2400
      TabIndex        =   3
      Top             =   1920
      Width           =   2175
   End
   Begin VB.TextBox txtID 
      Height          =   315
      Left            =   2400
      TabIndex        =   2
      Top             =   1560
      Width           =   1215
   End
   Begin VB.ListBox lstData 
      Height          =   2010
      Left            =   4800
      TabIndex        =   1
      Top             =   1080
      Width           =   3855
   End
   Begin VB.Label lblStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   600
      TabIndex        =   12
      Top             =   2640
      Width           =   1695
   End
   Begin VB.Label lblNamaGolongan 
      Caption         =   "Nama Golongan:"
      Height          =   255
      Left            =   600
      TabIndex        =   11
      Top             =   2280
      Width           =   1695
   End
   Begin VB.Label lblKodeGolongan 
      Caption         =   "Kode Golongan:"
      Height          =   255
      Left            =   600
      TabIndex        =   10
      Top             =   1920
      Width           =   1695
   End
   Begin VB.Label lblID 
      Caption         =   "ID:"
      Height          =   255
      Left            =   600
      TabIndex        =   13
      Top             =   1560
      Width           =   1695
   End
   Begin VB.Label lblInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   600
      TabIndex        =   0
      Top             =   3840
      Width           =   8175
   End
End
Attribute VB_Name = "frmTabelGolongan"
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
    
    response = GetAllTabelgolongan(1, 100)
    
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
                
                kode = ParseJSONValue(item, "kodegolongan")
                nama = ParseJSONValue(item, "namagolongan")
                
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
    Dim kodegolongan As String
    Dim namagolongan As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    kodegolongan = Trim(txtKodeGolongan.Text)
    namagolongan = Trim(txtNamaGolongan.Text)
    status = cmbStatus.Text
    
    If kodegolongan = "" Or namagolongan = "" Then
        MsgBox "Kode Golongan dan Nama Golongan harus diisi!", vbExclamation
        Exit Sub
    End If
    
    lblInfo.Caption = "Saving data..."
    response = CreateTabelgolongan(kodegolongan, namagolongan, status)
    
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
    Dim kodegolongan As String
    Dim namagolongan As String
    Dim status As String
    
    On Error GoTo ErrorHandler
    
    If Trim(txtID.Text) = "" Then
        MsgBox "ID harus diisi untuk update!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.Text)
    kodegolongan = Trim(txtKodeGolongan.Text)
    namagolongan = Trim(txtNamaGolongan.Text)
    status = cmbStatus.Text
    
    lblInfo.Caption = "Updating data..."
    response = UpdateTabelgolongan(id, kodegolongan, namagolongan, status)
    
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
    response = DeleteTabelgolonganById(id)
    
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
    Dim kodegolongan As String
    Dim response As String
    Dim data As String
    Dim statusValue As String
    
    On Error GoTo ErrorHandler
    
    If lstData.ListIndex < 0 Then Exit Sub
    
    selectedText = lstData.List(lstData.ListIndex)
    If InStr(selectedText, " - ") = 0 Then Exit Sub
    kodegolongan = Trim(Left(selectedText, InStr(selectedText, " - ") - 1))
    
    response = GetTabelgolonganByKode(kodegolongan)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        data = ParseJSONValue(response, "data")
        txtID.Text = ParseJSONValue(data, "id")
        txtKodeGolongan.Text = ParseJSONValue(data, "kodegolongan")
        txtNamaGolongan.Text = ParseJSONValue(data, "namagolongan")
        
        statusValue = ParseJSONValue(data, "status")
        If statusValue = "aktif" Then
            cmbStatus.ListIndex = 0
        Else
            cmbStatus.ListIndex = 1
        End If
        
        lblInfo.Caption = "Data loaded: " & kodegolongan
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub ClearForm()
    txtID.Text = ""
    txtKodeGolongan.Text = ""
    txtNamaGolongan.Text = ""
    cmbStatus.ListIndex = 0
End Sub


