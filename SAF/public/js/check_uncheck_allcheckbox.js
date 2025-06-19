/**
 * Check or uncheck all id[] named checkboxes
 */
function select_all(){

    let i;
    let checkboxes = document.getElementsByName('id[]');
    let selectAll = document.getElementById('check');
    if(selectAll.value === 'select'){
        for(i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        selectAll.value = "deselect";
    }
    else{
        for(i in checkboxes){
            checkboxes[i].checked = '';
        }
        selectAll.value = "select";
    }
}