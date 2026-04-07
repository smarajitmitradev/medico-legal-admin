<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Toast UI Editor + Graph (Styled)</title>

    <!-- Toast UI Editor -->
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

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
            margin-bottom: 15px;
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

        .chart {
            width: 100%;
            max-width: 600px;
            height: 300px !important;
            /* 🔥 force height */
            margin: 20px auto;
            position: relative;
        }

        .chart canvas {
            width: 100% !important;
            height: 100% !important;
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
            min-height: 150px;
            box-shadow: inset 0 4px 6px rgba(0, 0, 0, 0.03);
        }

        #editor {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Enter Graph Data</h3>

        <div class="input-group">
            <label>X Values</label>
            <input type="text" id="xValues" value="10,20,30,40,50" />
        </div>

        <div class="input-group">
            <label>Y Values</label>
            <input type="text" id="yValues" value="223,132,532,242,300" />
        </div>

        <button onclick="insertGraph()">Insert Graph</button>

        <hr />

        <div id="editor"></div>

        <h3>Preview</h3>
        <div id="preview"></div>
    </div>

    <script>
    const editor = new toastui.Editor({
        el: document.querySelector('#editor'),
        height: '400px',
        initialEditType: 'wysiwyg',
        previewStyle: 'tab',
        usageStatistics: false,
        customHTMLSanitizer: html => html,
        customHTMLRenderer: {
            htmlBlock: {
                div(node) {
                    return [{
                        type: 'openTag',
                        tagName: 'div',
                        attributes: node.attrs || {}
                    }, {
                        type: 'closeTag',
                        tagName: 'div'
                    }];
                }
            }
        }
    });

    function insertGraph() {
    const x = document.getElementById("xValues").value.split(',').map(Number);
    const y = document.getElementById("yValues").value.split(',').map(Number);

    const chartData = {
        type: "line",
        labels: x,
        data: y
    };

    const rawHtml = `<div class="chart" data-chart='${encodeURIComponent(JSON.stringify(chartData))}'></div>`;

    // ✅ Insert as code block
    const codeBlock = "```html\n" + rawHtml + "\n```\n\n";

    editor.insertText(codeBlock);

    triggerRender();
}

    function renderCharts(container) {
    const charts = container.querySelectorAll('.chart');

    charts.forEach(el => {
        // ✅ ALWAYS reset content (fix disappearing issue)
        el.innerHTML = "";

        const config = JSON.parse(decodeURIComponent(el.dataset.chart));

        const canvas = document.createElement("canvas");
        el.appendChild(canvas);

        new Chart(canvas, {
            type: config.type,
            data: {
                labels: config.labels,
                datasets: [{
                    label: 'Sample',
                    data: config.data,
                    borderColor: '#007bff',
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
}

function renderAll() {
    let content = editor.getMarkdown();

    // ✅ Convert code block → real HTML
    const html = content.replace(/```html\n([\s\S]*?)\n```/g, (match, code) => {
        return code;
    });

    // ✅ Bottom preview (your custom preview)
    const preview = document.getElementById("preview");
    preview.innerHTML = html;
    renderCharts(preview);

    // ✅ 🔥 Markdown RIGHT-SIDE preview inside editor
    const mdPreview = document.querySelector('.toastui-editor-md-preview');

    if (mdPreview) {
        mdPreview.innerHTML = html; // overwrite default preview
        renderCharts(mdPreview);
    }
}

function triggerRender() {
    setTimeout(renderAll, 300);
}

// content change
editor.on('change', triggerRender);

// mode switch
editor.on('changeMode', () => {
    setTimeout(renderAll, 400);
});

// 🔥 when clicking preview tab
document.addEventListener('click', (e) => {
    if (e.target.closest('.toastui-editor-tab-preview')) {
        setTimeout(renderAll, 400);
    }
});

    // initial
    window.onload = () => {
        renderAll();
    };
</script>
</body>

</html>