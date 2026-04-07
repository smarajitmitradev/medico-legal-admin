<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CKEditor 5 + Chart.js + Full Tools</title>


<!-- CKEditor 5 SUPER BUILD -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>

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

    h3 {
        margin-bottom: 10px;
        color: #2c3e50;
    }

    hr {
        margin: 30px 0;
        border: none;
        height: 1px;
        background: #ddd;
    }

    .container {
        max-width: 900px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .input-group {
        margin-bottom: 15px;
    }

    input[type="text"] {
        width: 100%;
        max-width: 400px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        outline: none;
        transition: 0.2s;
        font-size: 14px;
    }

    input[type="text"]:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
    }

    button {
        padding: 10px 18px;
        background: linear-gradient(135deg, #4a90e2, #357abd);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }

    button:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(74, 144, 226, 0.3);
    }

    button:disabled {
        background: #aaa;
        cursor: not-allowed;
        box-shadow: none;
    }

    #editor {
        margin-top: 20px;
        border-radius: 10px;
        overflow: hidden;
    }

    .ck-editor__editable_inline {
        min-height: 200px;
        padding: 15px;
    }

    .chart {
        max-width: 600px;
        margin: 20px auto;
        border-radius: 10px;
        padding: 15px;
        background: #ffffff;
        border: 1px solid #e0e0e0;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    #preview {
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        background: #fafafa;
        border: 1px solid #ddd;
        min-height: 100px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
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

    <button id="insertBtn" onclick="insertGraph()" disabled>Insert Graph</button>

    <hr>

    <div id="editor"></div>

    <h3>Preview</h3>
    <div id="preview"></div>
</div>

<script>
    let editor;

    function CustomChartPlugin(editor) {
        const conversion = editor.conversion;

        editor.model.schema.register("chart", {
            allowWhere: "$block",
            isObject: true,
            allowAttributes: ["dataChart"]
        });

        conversion.for("upcast").elementToElement({
            view: { name: "div", classes: "chart" },
            model: (viewElement, { writer }) => {
                const dataChart = viewElement.getAttribute("data-chart");
                return writer.createElement("chart", { dataChart });
            }
        });

        conversion.for("dataDowncast").elementToElement({
            model: "chart",
            view: (modelItem, { writer }) => {
                const dataChart = modelItem.getAttribute("dataChart");
                return writer.createContainerElement("div", {
                    class: "chart",
                    "data-chart": dataChart
                });
            }
        });

        conversion.for("editingDowncast").elementToElement({
            model: "chart",
            view: (modelItem, { writer }) => {
                const dataChart = modelItem.getAttribute("dataChart");

                let text = "[Graph placeholder]";
                try {
                    const parsed = JSON.parse(decodeURIComponent(dataChart));
                    text = "Graph X: " + parsed.labels.join(",") + " | Y: " + parsed.data.join(",");
                } catch (e) {}

                const div = writer.createContainerElement("div", {
                    class: "chart",
                    "data-chart": dataChart
                });

                writer.insert(writer.createPositionAt(div, 0), writer.createText(text));
                return div;
            }
        });
    }

    CKEDITOR.ClassicEditor.create(document.querySelector("#editor"), {
        extraPlugins: [CustomChartPlugin],
        removePlugins: [
            "RealTimeCollaborativeComments",
            "RealTimeCollaborativeTrackChanges",
            "RealTimeCollaborativeRevisionHistory",
            "PresenceList",
            "Comments",
            "TrackChanges",
            "TrackChangesData",
            "RevisionHistory",
            "DocumentOutline",
            "TableOfContents",
            "FormatPainter",
            "Template",
            "SlashCommand",
            "PasteFromOfficeEnhanced",
            "Pagination",
            "WProofreader"
        ],
        toolbar: {
            items: [
                "sourceEditing","|","heading","|",
                "bold","italic","underline","strikethrough","|",
                "fontSize","fontFamily","fontColor","fontBackgroundColor","|",
                "alignment","|",
                "bulletedList","numberedList","|",
                "outdent","indent","|",
                "link","insertTable","mediaEmbed","blockQuote","imageUpload","|",
                "code","codeBlock","|",
                "undo","redo"
            ]
        }
    })
    .then(newEditor => {
        editor = newEditor;
        document.getElementById("insertBtn").disabled = false;

        editor.model.document.on("change:data", () => {
            renderCharts();
        });
    })
    .catch(console.error);

    function insertGraph() {
        const x = document.getElementById("xValues").value.split(",").map(Number);
        const y = document.getElementById("yValues").value.split(",").map(Number);

        const chartData = {
            type: "line",
            labels: x,
            data: y
        };

        const dataChart = encodeURIComponent(JSON.stringify(chartData));

        editor.model.change(writer => {
            const chartElement = writer.createElement("chart", { dataChart });

            const root = editor.model.document.getRoot();
            const selection = editor.model.document.selection;
            let insertPosition = selection.getFirstPosition();

            if (!insertPosition || insertPosition.root !== root) {
                insertPosition = root.getChildCount()
                    ? root.getChild(root.childCount - 1).getEndPosition()
                    : root.getStartPosition();
            }

            writer.insert(chartElement, insertPosition);
        });

        renderCharts();
    }

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
            } catch (e) {
                return;
            }

            const canvas = document.createElement("canvas");
            el.innerHTML = "";
            el.appendChild(canvas);

            new Chart(canvas, {
                type: config.type,
                data: {
                    labels: config.labels,
                    datasets: [{
                        label: "Sample",
                        data: config.data,
                        borderColor: "#4a90e2",
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
</script>


</body>
</html>
