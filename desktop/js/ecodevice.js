$('.eqLogicAction[data-action=hide]').on('click', function () {
    var eqLogic_id = $(this).attr('data-eqLogic_id');
    $('.sub-nav-list').each(function () {
		if ( $(this).attr('data-eqLogic_id') == eqLogic_id ) {
			$(this).toggle();
		}
    });
    return false;
});

function addCmdToTable(_cmd) {
   if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    if (init(_cmd.type) == 'info') {
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" >';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 200px;" placeholder="{{Nom}}">';
        if (init(_cmd.logicalId) == 'nbimpulsionjour') {
			tr += ' Uniquement valable en mode non Fuel';
		}
        if (init(_cmd.logicalId) == 'tempsfonctionnement' || init(_cmd.logicalId) == 'tempsfonctionnementminute') {
			tr += ' Uniquement valable en mode Fuel';
		}
		tr += '</td>';
		tr += '<td class="expertModeVisible">';
        tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
        tr += '<td>';
        if (init(_cmd.logicalId) == 'nbimpulsionminute' || init(_cmd.logicalId) == 'tempsfonctionnementminute') {
			tr += '<textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="calcul" style="height : 33px;" placeholder="{{Calcul}}"></textarea> {{(utiliser #brut# dans la formule)}}';
		}
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unite}}">';
        if (init(_cmd.logicalId) == 'tempsfonctionnement' || init(_cmd.logicalId) == 'tempsfonctionnementminute') {
			tr += '{{Mettre 1 dans Débit gicleur en L/h}})';
		}
        tr += '</td>';
        tr += '<td>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized"/> {{Historiser}}<br/></span>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
		tr += '<input class="cmdAttr" data-l1key="categorie" style="display : none;">';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        }
        tr += '</td>';
		table_cmd = '#table_cmd';
		if ( $(table_cmd+'_'+_cmd.eqType ).length ) {
			table_cmd+= '_'+_cmd.eqType;
		}
        $(table_cmd+' tbody').append(tr);
        $(table_cmd+' tbody tr:last').setValues(_cmd, '.cmdAttr');
    }
}

$('#bt_goCarte').on('click', function() {
    $('#md_modal').dialog({title: "{{Accèder à l'interface de l'Ecodevice}}"});
	window.open('http://'+$('.eqLogicAttr[data-l2key=username]').value()+':'+$('.eqLogicAttr[data-l2key=password]').value()+'@'+$('.eqLogicAttr[data-l2key=ip]').value()+':'+$('.eqLogicAttr[data-l2key=port]').value()+'/');
});

$('#bt_configPush').on('click', function() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des donnÃ©es au fichier php
        url: "plugins/ecodevice/core/ajax/ecodevice.ajax.php", // url du fichier php
        data: {
            action: "configPush",
			id: $('.li_eqLogic.active').attr('data-eqLogic_id')
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionnÃ©
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
        }
    });
});

$("#table_cmd_ecodevice_compteur").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_cmd_ecodevice_teleinfo").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
