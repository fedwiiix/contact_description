class SettingsClass {
    init() {

        document.getElementById('import-file').addEventListener('change', this.importJson);
        document.getElementById('export-json-file').addEventListener('click', this.exportJson);
        document.getElementById('export-csv-file').addEventListener('click', this.exportCsv);

    }

    importJson = (event) => {
        var reader = new FileReader();
        reader.onload = (event) => { this.onReaderLoad(event) }
        reader.readAsText(event.target.files[0]);
    }

    onReaderLoad(event) {
        try {
            var jsonObj = JSON.parse(event.target.result);



            jsonObj.foreach(element => {
                console.log(element)

            })

        } catch (error) {
            toast("Bad file format", 3)
        }

    }

    getExportData(callback) {
        ajaxRequest("/export", "GET", null, (exportJson) => {
            callback(exportJson)
        });
    }

    exportJson = () => {
        this.getExportData((json) => {
            this.download('contacts.json', JSON.stringify(json), 'text/json')
        })
    }

    exportCsv = () => {
        this.getExportData((json) => {

            var data = this.convertToCSV(json)
            this.download('contacts.csv', data, 'text/csv')
        })
    }

    download(filename, data, mime) {
        var blob = new Blob([data], { type: mime }),
            e = document.createEvent('MouseEvents'),
            a = document.createElement('a')

        a.download = filename
        a.href = window.URL.createObjectURL(blob)
        a.dataset.downloadurl = [mime, a.download, a.href].join(':')
        e.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null)
        a.dispatchEvent(e)
    }

    jsonTocsv(json) {
        var fields = Object.keys(json[0])
        var replacer = function(key, value) { return value === null ? '' : value }
        var csv = json.map(function(row) {
            return fields.map(function(fieldName) {
                return JSON.stringify(row[fieldName], replacer)
            }).join(',')
        })
        csv.unshift(fields.join(',')) // add header column
        csv = csv.join('\r\n');
        console.log(csv)
    }

    convertToCSV(json) {
        if (!json || !json.length) {
            return ''
        }
        var str = '';

        Object.keys(json[0]).forEach(val => {
            str += (str == '' ? '' : ',') + val;
        })
        str += '\r\n';

        json.forEach(element => {
            let line = '';
            Object.values(element).forEach(val => {
                if (Array.isArray(val)) {

                    let newVal = ""
                    val.forEach(element => {
                        newVal += element.name + ',';
                    })
                    line += (line == '' ? '' : ',') + this.csvCleanString(newVal.slice(0, -1));
                } else {
                    line += (line == '' ? '' : ',') + this.csvCleanString(val);
                }
            })
            str += line + '\r\n';
        })
        return str;
    }

    csvCleanString(str) {
        if (!str || !str.length) { return ''; }
        str = str.replace(/"/g, '""')
        return `"${str}"`
    }
}

var Settings = new SettingsClass();