Set objFSO = CreateObject("Scripting.FileSystemObject")

logfolder    = "C:\MonitorProcLog\"
sLogFileName = "alertProc_" & Year(Now) & Month(Now) & Day(Now) & "_" & Hour(Now) & Minute(Now) & Second(Now) & "_log.txt"
fullPath = logfolder & sLogFileName
Set objFile = objFSO.CreateTextFile(sLogFileName,True)
logfile = objFSO.BuildPath(logfolder, sLogFileName)
Set MyLog = objFSO.OpenTextFile(logfile, 8, True)
Set objShell = WScript.CreateObject("WScript.Shell")
Set objExecObject = objShell.Exec("tasklist /v")
' Set objExecObject = objShell.Exec("cmd.exe /C tasklist /v /fi ""STATUS eq running""")

strText = ""

Do While Not objExecObject.StdOut.AtEndOfStream
    strText = strText & objExecObject.StdOut.ReadLine() & vbCrLf 
Loop
    MyLog.WriteLine strText
    MyLog.Close

    



