<!DOCTYPE html>
<html>
  <head>
    <title>BUR Management Site</title>
    <link href="main_styles.css" type="text/css" rel="stylesheet" /> 
    <script type="text/javascript">
    function redirect(newPath) {
      window.location.href = `${window.location.protocol}//${window.location.hostname}:${window.location.port}/${newPath}`;
    }
    </script>
  </head>
  <body>
    <nav>
      <h1>BUR Management System</h1>
    </nav>
    <div class="main-content">
      <div class="flex-box">
          <button onclick="redirect('patient.php')">Patient Dashboard</button>
          <button onclick="redirect('admin.php')">Admin Dashboard</button>
      </div>
    </div>
  </body>
</html>
