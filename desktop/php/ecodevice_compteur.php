    <div class="col-lg-9 eqLogic ecodevice_compteur" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>
                    {{Général}}
                </legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nom du compteur}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'ecodevice}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >{{Objet parent}}</label>
                    <div class="col-lg-3">
                        <select class="form-control eqLogicAttr" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Catégorie}}</label>
                    <div class="col-lg-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >{{Activer}}</label>
                    <div class="col-md-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                    </div>
                    <label class="col-lg-2 control-label" >{{Visible}}</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <legend>{{Indicateurs}}</legend>
        <table id="table_cmd_ecodevice_compteur" class="table table-bordered table-condensed ecodevice_compteur">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 230px;">{{Nom}}</th>
                    <th style="width: 110px;">{{Sous-Type}}</th>
                    <th>{{Valeur}}</th>
                    <th style="width: 100px;">{{Unité}}</th>
                    <th style="width: 200px;">{{Paramètres}}</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
