<!DOCTYPE html>
<html>
<head>
    <title>Toast UI Chart Integration</title>

    <!-- Toast UI Editor -->
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart {
            width: 100%;
            height: 300px;
            margin-top: 20px;
        }

        /* ===== Page Styling ===== */
body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
    color: #2c3e50;
}

/* Headings */
h3 {
    margin-bottom: 10px;
    font-weight: 600;
}

/* ===== Input Section ===== */
input {
    padding: 10px 12px;
    border: 1px solid #dcdfe6;
    border-radius: 8px;
    outline: none;
    width: 260px;
    transition: all 0.2s ease;
    font-size: 14px;
}

input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
}

/* Button Styling */
button {
    background: linear-gradient(135deg, #4a90e2, #007bff);
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.25s ease;
}

button:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

button:active {
    transform: scale(0.97);
}

/* ===== Editor Container ===== */
#editor {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    margin-top: 10px;
}

/* Toast UI Toolbar */
.toastui-editor-defaultUI-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #eee;
}

/* Editor content */
.toastui-editor-contents {
    font-size: 15px;
    line-height: 1.6;
}

/* ===== Preview Box ===== */
#preview {
    background: #ffffff;
    border-radius: 12px;
    padding: 15px;
    margin-top: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    min-height: 100px;
}

/* ===== Chart Container ===== */
.chart {
    width: 100%;
    height: 300px;
    margin-top: 20px;
    padding: 10px;
    border-radius: 12px;
    background: linear-gradient(145deg, #ffffff, #f1f3f6);
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05),
                0 6px 16px rgba(0,0,0,0.08);
}

/* ===== Divider ===== */
hr {
    border: none;
    height: 1px;
    background: #e6e9ef;
    margin: 25px 0;
}

/* ===== Scrollbar (modern look) ===== */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-thumb {
    background: #cfd8dc;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #b0bec5;
}
    </style>
</head>
<body>

<h3>Enter Graph Data</h3>

X Values: <input type="text" id="xValues" value="10,20,30,40,50"><br><br>
Y Values: <input type="text" id="yValues" value="223,132,532,242,300"><br><br>

<button onclick="insertGraph()">Insert Graph</button>

<hr>

<div id="editor"></div>

<h3>Preview</h3>
<div id="preview" style="border:1px solid #ccc; padding:10px;"></div>

<br>
<button onclick="saveContent()">Save (Get Final HTML)</button>

<script>
const editor = new toastui.Editor({
    el: document.querySelector('#editor'),
    height: '400px',
    initialEditType: 'markdown', // KEEP markdown
    previewStyle: 'tab',
    usageStatistics: false
});


// ✅ Insert placeholder
function insertGraph() {
    const x = document.getElementById("xValues").value.split(',').map(Number);
    const y = document.getElementById("yValues").value.split(',').map(Number);

    const chartData = {
        type: "line",
        labels: x,
        data: y
    };

    const placeholder = `[chart:${encodeURIComponent(JSON.stringify(chartData))}]`;

    editor.replaceSelection(placeholder);

    triggerRender();
}


// ✅ Convert placeholder → div
function convertPlaceholdersToHTML(html) {
    return html.replace(/\[chart:(.*?)\]/g, (_, data) => {
        return `<div class="chart" data-chart="${data}"></div>`;
    });
}


// ✅ Render charts
function renderCharts(container) {
    const charts = container.querySelectorAll('.chart');

    charts.forEach(el => {
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


// ✅ Render preview (custom + editor preview)
function renderPreview() {
    const preview = document.getElementById("preview");

    let html = editor.getHTML();

    html = convertPlaceholdersToHTML(html);

    preview.innerHTML = html;

    renderCharts(preview);
}


// ✅ Trigger render everywhere
function triggerRender() {
    setTimeout(() => {
        renderPreview();

        // ALSO render inside Toast UI preview tab
        const editorPreview = document.querySelector('.toastui-editor-contents');
        if (editorPreview) {
            let html = editorPreview.innerHTML;

            html = convertPlaceholdersToHTML(html);
            editorPreview.innerHTML = html;

            renderCharts(editorPreview);
        }

    }, 100);
}


// Events
editor.on('change', triggerRender);
editor.on('changeMode', triggerRender);

window.onload = () => triggerRender();


// ✅ FINAL SAVE OUTPUT (with real <div>)
function saveContent() {
    let html = editor.getHTML();

    html = convertPlaceholdersToHTML(html);

    console.log("Final HTML:", html);

    alert("Check console for final HTML output");
}
</script>

</body>
</html>