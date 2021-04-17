<!DOCTYPE html>
<html>
  <head>
    <title>BUR Admin Page</title>
    <meta charset="UTF-8" />
    <link href="main_styles.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
    function redirect(newPath) {
      window.location.href = `${window.location.protocol}//${window.location.hostname}:${window.location.port}/${newPath}`;
    }
    </script>
  </head>
  <body>
    <nav>
      <h1>BUR Admin Dashboard</h1>
    </nav>
    <div class="main-content">
      <a href="index.php">Go back</a>
      <div class="grid">
        <div class="action-card" onclick="redirect('admin/add_batch.php')">
          <h2>Add Batch</h2>
          <p>Add a batch of vaccine doses to the system and schedule waitlisted patients</p>
        </div>
      </div>
      <div class="action-card" onclick="redirect('admin/list_waiting.php')">
        <h2>Waiting List</h2>
        <p>Generate list of patients that are still waiting for the vaccine</p>
      </div>
      <div class="action-card" onclick="redirect('admin/list_scheduled.php')">
        <h2>Scheduled List</h2>
        <p>Generate list of patients that have scheduled for the vaccine</p>
      </div>
    </div>
  </body>
</html>
