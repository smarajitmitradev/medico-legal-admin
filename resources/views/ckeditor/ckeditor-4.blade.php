<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CKEditor + Chart.js (Styled Professional)</title>

    <!-- ✅ Free CKEditor version -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #eef2f7, #f8fbff);
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        hr {
            border: none;
            height: 1px;
            background: #ddd;
            margin: 30px 0;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 123, 255, 0.3);
        }

        button:disabled {
            background: #aaa;
            cursor: not-allowed;
            box-shadow: none;
        }

        .chart {
            max-width: 600px;
            height: 300px;
            margin: 20px auto;
            border-radius: 10px;
            border: 2px dashed #999;
            background: #f9f9f9;
            position: relative;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            padding: 10px;
        }

        .chart::before {
            content: "📊 Chart";
            position: absolute;
            top: 10px;
            left: 10px;
            color: #666;
            font-size: 14px;
        }

        #preview {
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            background: #fafafa;
            border: 1px solid #ddd;
            min-height: 100px;
            box-shadow: inset 0 4px 6px rgba(0, 0, 0, 0.03);
        }

        textarea#editor {
            border-radius: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Enter Graph Data</h3>

        <div class="input-group">
            <label>X Values</label>
            <input type="text" id="xValues" value="10,20,30,40,50">
        </div>

        <div class="input-group">
            <label>Y Values</label>
            <input type="text" id="yValues" value="223,132,532,242,300">
        </div>

        <button id="insertBtn" disabled style="margin-top: 8px;">Insert Graph</button>

        <hr>

        <textarea id="editor"></textarea>

        <h3>Preview</h3>
        <div id="preview"></div>
    </div>

    <script>
        let editor;

        // ✅ Initialize editor
        window.onload = function () {
            if (typeof CKEDITOR === 'undefined') {
                console.error("CKEditor failed to load");
                return;
            }

            editor = CKEDITOR.replace('editor', {
                height: 300,
                allowedContent: true,
            });

            CKEDITOR.on('instanceReady', function () {
                document.getElementById('insertBtn').disabled = false;
                editor.on('change', renderCharts);
            });
        };

        // ✅ Insert chart placeholder into editor
        function insertGraph() {
            if (!editor) return;

            const x = document.getElementById("xValues").value
                .split(",")
                .map(v => v.trim());

            const y = document.getElementById("yValues").value
                .split(",")
                .map(v => v.trim());

            if (x.length !== y.length || x.some(v => v === "") || y.some(v => v === "")) {
                alert("Invalid data! Make sure X and Y values are valid and same length.");
                return;
            }

            const chartData = {
                type: "line",
                labels: x,
                data: y
            };

            const encoded = encodeURIComponent(JSON.stringify(chartData));

            const html = `
                <div class="chart" data-chart="${encoded}">
                    X: [${x.join(", ")}] <br>
                    Y: [${y.join(", ")}]
                </div>
            `;

            editor.insertHtml(html);
            renderCharts();
        }

        // ✅ Render charts in preview only
        function renderCharts() {
            const preview = document.getElementById("preview");
            const content = editor.getData();
            preview.innerHTML = content;

            const charts = preview.querySelectorAll(".chart");
            charts.forEach(el => {
                const dataChart = el.getAttribute("data-chart");
                if (!dataChart) return;

                let config;
                try {
                    config = JSON.parse(decodeURIComponent(dataChart));
                } catch (e) { return; }

                el.innerHTML = '';
                const canvas = document.createElement("canvas");
                el.appendChild(canvas);

                new Chart(canvas, {
                    type: config.type,
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: "Sample Data",
                            data: config.data,
                            borderColor: "#007bff",
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        }

        document.getElementById('insertBtn').addEventListener('click', insertGraph);
    </script>
</body>

</html>
