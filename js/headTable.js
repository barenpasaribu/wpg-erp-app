function headTable() {
    /* getValue
     * Fungsi umum untuk mengekstrak nilai berdasarkan id
     */
    this.getValue = function(id) {
        var tmp = document.getElementById(id);
        
        if(tmp) {
            if(tmp.options) {
                // Options
                return tmp.options[tmp.selectedIndex].value;
            } else if(tmp.nodeType=='checkbox') {
                // Checkbox
                if(tmp.checked==true) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                if(tmp.value) {
                    // Textbox
                    return tmp.value;
                } else {
                    // Other Tag
                    return tmp.getAttribute('value');
                }
            }
        } else {
            return false;
        }
    }
}

theHTab = new headTable();