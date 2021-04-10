<!DOCTYPE html>
<html>
  <head>
    <title>BUR Management Site</title>
    <script type="text/javascript">
    function redirect(newPath) {
      window.location.href = `${window.location.protocol}//${window.location.hostname}:${window.location.port}/${newPath}`;
    }
    </script>
  </head>
  <body>
    <h1>BUR Management Dashboard</h1>
    <h2>Are you a</h2>
    <button onclick="redirect('patient.php')">Patient</button> or 
    <button onclick="redirect('admin.php')">Admin</button>?
  </body>
</html>
