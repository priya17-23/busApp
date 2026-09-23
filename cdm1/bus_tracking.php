<?php
session_start();
$canChange = !empty($_SESSION['admin']) || !empty($_SESSION['driver']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysuru College Bus Tracker</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body{margin:0;font-family:Segoe UI,Arial}
        header{background:#840404;color:#fff;padding:12px 16px;display:flex;justify-content:space-between;align-items:center}
        #map{height:72vh}
        .wrap{display:flex;gap:12px;padding:12px}
        .side{width:360px;background:#fff;border-radius:8px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
        .controls{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
        .bus{padding:8px;border-radius:6px;background:#f8f9fa;margin-bottom:8px;cursor:pointer;transition:all .12s}
        .bus:hover{transform:translateY(-2px)}
        .stats{display:flex;gap:12px}
        .stat{background:#fff;padding:8px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,.04);text-align:center;min-width:80px}
        .stat .val{font-weight:700;font-size:1.1rem}
        .label-small{font-size:.85rem;color:#666}
        .status-controls{margin-top:10px}
        .btn{padding:7px 10px;border-radius:6px;border:1px solid #ddd;background:#fff;cursor:pointer}
        .btn.yellow{background:#ffd700;border-color:#e6b800}
    </style>
</head>
<body>
    <header>
        <div><strong>GSSSIETW Bus Tracker</strong></div>
        <div class="stats">
            <div class="stat"><div class="val" id="totalCount">0</div><div class="label-small">Total</div></div>
            <div class="stat"><div class="val" id="activeCount">0</div><div class="label-small">Active</div></div>
            <div class="stat"><div class="val" id="delayedCount">0</div><div class="label-small">Delayed</div></div>
            <div class="stat"><div class="val" id="inactiveCount">0</div><div class="label-small">Inactive</div></div>
        </div>
    </header>

    <div class="wrap">
        <div id="map" style="flex:1"></div>

        <div class="side">
            <div class="controls">
                <select id="routeSelect" class="btn">
                    <option value="all">All Routes</option><option value="1">Route 1</option><option value="2">Route 2</option>
                </select>

                <select id="statusSelect" class="btn">
                    <option value="all">All Status</option><option value="active">Active</option><option value="inactive">Inactive</option><option value="delayed">Delayed</option>
                </select>

                <button id="refresh" class="btn">Refresh</button>

                <?php if ($canChange): ?>
                    <button id="setAllActive" class="btn yellow">Set All Active</button>
                    <button id="setAllDelayed" class="btn">Set All Delayed</button>
                    <button id="setAllInactive" class="btn">Set All Inactive</button>
                <?php endif; ?>
            </div>

            <div id="busList" style="max-height:58vh;overflow:auto"></div>

            <?php if ($canChange): ?>
                <div class="status-controls" id="selectedControls" style="display:none">
                    <div class="label-small">Selected Bus: <span id="selBusLabel"></span></div>
                    <select id="selStatus" class="btn" style="width:48%">
                        <option value="active">Active</option><option value="delayed">Delayed</option><option value="inactive">Inactive</option>
                    </select>
                    <button id="applyStatus" class="btn" style="width:48%">Apply</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([12.2958,76.6394],13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const routes = {
          1:{color:'#138808',stops:[[12.307,76.65],[12.315,76.63],[12.325,76.61]]},
          2:{color:'#ff9933',stops:[[12.307,76.65],[12.295,76.66],[12.285,76.67]]}
        };

        let buses=[], markers={}, selectedBusId = null;

        // generate sample buses
        function initBuses(n=40){
          buses = Array.from({length:n},(_,i)=>({
            id:i+1,
            number:`MYS-${String(i+1).padStart(2,'0')}`,
            route: Math.random()>0.5?1:2,
            lat:12.2958+(Math.random()-0.5)*0.05,
            lng:76.6394+(Math.random()-0.5)*0.05,
            status: ['active','inactive','delayed'][Math.floor(Math.random()*3)]
          }));
          updateAll();
        }

        function clearMarkers(){ Object.values(markers).forEach(m=>map.removeLayer(m)); markers={}; }

        function updateAll(){
          clearMarkers();
          buses.forEach(b=>{
            const cls = b.status;
            const html = `<div class="label ${cls}" style="padding:6px 8px;border-radius:6px;background:${cls==='active'?'#28a745':cls==='delayed'?'#ffc107':'#6c757d'};color:#fff;font-weight:600">${b.number}</div>`;
            const icon = L.divIcon({className:'', html, iconSize:[80,30]});
            const m = L.marker([b.lat,b.lng],{icon}).addTo(map).bindPopup(`<b>${b.number}</b><br>Route ${b.route}<br>Status: ${b.status}`);
            markers[b.id]=m;
            // pointer UX
            m.on('add',()=>{
              const el=m.getElement(); if(!el) return;
              el.style.cursor='pointer';
              el.addEventListener('mouseenter',()=>m.openPopup());
              el.addEventListener('mouseleave',()=>m.closePopup());
            });
            // reflect selection highlight
            m.on('click',()=> selectBus(b.id));
          });
          renderList();
          updateCounts();
        }

        function renderList(){
          const routeFilter = document.getElementById('routeSelect').value;
          const statusFilter = document.getElementById('statusSelect').value;
          const list = document.getElementById('busList');
          const filtered = buses.filter(b=>(routeFilter==='all'||b.route==routeFilter)&&(statusFilter==='all'||b.status===statusFilter));
          list.innerHTML = filtered.map(b=>`<div class="bus" data-id="${b.id}"><strong>${b.number}</strong> • R${b.route} • ${b.status}</div>`).join('') || '<div class="label-small">No buses</div>';
          // attach click handlers
          list.querySelectorAll('.bus').forEach(el=>el.addEventListener('click',()=>selectBus(+el.dataset.id)));
        }

        function selectBus(id){
          selectedBusId = id;
          const b = buses.find(x=>x.id===id);
          if(!b) return;
          map.setView([b.lat,b.lng],15,{animate:true});
          markers[id].openPopup();
          <?php if ($canChange): ?>
          document.getElementById('selectedControls').style.display='block';
          document.getElementById('selBusLabel').textContent = b.number;
          document.getElementById('selStatus').value = b.status;
          <?php endif; ?>
        }

        function updateCounts(){
          const total = buses.length;
          const active = buses.filter(b=>b.status==='active').length;
          const delayed = buses.filter(b=>b.status==='delayed').length;
          const inactive = buses.filter(b=>b.status==='inactive').length;
          document.getElementById('totalCount').textContent = total;
          document.getElementById('activeCount').textContent = active;
          document.getElementById('delayedCount').textContent = delayed;
          document.getElementById('inactiveCount').textContent = inactive;
        }

        function refresh(){
          buses.forEach(b=>{ b.lat += (Math.random()-0.5)*0.001; b.lng += (Math.random()-0.5)*0.001; if(Math.random()<0.04) b.status=['active','inactive','delayed'][Math.floor(Math.random()*3)]; });
          updateAll();
        }

        function setBusStatus(id,status){
          const b = buses.find(x=>x.id===id); if(!b) return;
          b.status = status;
          updateAll();
        }
        function setAllStatus(status){
          buses.forEach(b=>b.status=status);
          updateAll();
        }

        // UI bindings
        document.getElementById('routeSelect').addEventListener('change', renderList);
        document.getElementById('statusSelect').addEventListener('change', renderList);
        document.getElementById('refresh').addEventListener('click', refresh);

        <?php if ($canChange): ?>
        document.getElementById('applyStatus').addEventListener('click', ()=>{
          const s = document.getElementById('selStatus').value;
          if(selectedBusId) setBusStatus(selectedBusId,s);
        });
        document.getElementById('setAllActive').addEventListener('click', ()=> setAllStatus('active'));
        document.getElementById('setAllDelayed').addEventListener('click', ()=> setAllStatus('delayed'));
        document.getElementById('setAllInactive').addEventListener('click', ()=> setAllStatus('inactive'));
        <?php endif; ?>

        // init
        initBuses(40);
        setInterval(refresh,10000);
    </script>
</body>
</html>
