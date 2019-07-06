class C_Variable {
    // Constructor
    constructor(id, idAPP, nombre, valor, tipo, descripcion) {
        this.id = id || null;
        this.idAPP = idAPP || null;
        this.nombre = nombre || null;
        this.valor = valor || null;
        this.tipo = tipo || null;
        this.descripcion = descripcion || null;

    }
    get clearVariables() {
        $("#variablesXApp").html("");
    }

    get CargarVariablesbyIDApp() {
        var miAccion = 'CargarVariablesbyIDApp';
        $.ajax({
            type: "POST",
            url: "class/Variable.php",
            data: {
                action: miAccion,
                id: app.id
            }
        })
            .done(function (e) {
                // if (e!="false")
                c_Variable.drawVariables(e);
            })
            .fail(function (e) {
                // app.showError(e);
            });
    }

    get UpdateVariables() {
        var miAccion = 'UpdateVariables';

        $.ajax({
            type: "POST",
            url: "class/Variable.php",
            data: {
                action: miAccion,
                obj: JSON.stringify(app)
            }
        })
            .done(function (e) {
                // Swal.fire({
                //     position: 'top-end',
                //     type: 'success',
                //     title: 'Variables Actualizadas',
                //     showConfirmButton: false,
                //     timer: 1500
                //   })

                $("#modal_variables").modal("toggle");
            })
            .fail(function (e) {
                // app.showError(e);
            });
    }

    UpdateVariablesByXML(file) {

        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xml)$/;
        if (regex.test($("#fileUpload").val().toLowerCase())) {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var xmlDoc = $.parseXML(e.target.result);
                    var envEntry = $(xmlDoc).find("env-entry");

                    //Add the data rows.
                    var env_vars = [];
                    $(envEntry).each(function () {
                        var entrys = {};
                        
                        $(this).children().each(function (index, variable) {
                            
                            switch (variable.tagName) {
                                case "description":
                                entrys.descripcion = variable.textContent;
                                  break;
                                case "env-entry-type":
                                  entrys.tipo = variable.textContent;
                                  break;
                                case "env-entry-name":
                                    entrys.nombre = variable.textContent;
                                break;
                                case "env-entry-value":
                                    entrys.valor = variable.textContent;
                                break;
                            }                                  
                        });
                        env_vars.push(entrys);
                    });
                    $(env_vars).each(function (index, variable) {
                        $("#variablesXApp").append(c_Variable.loadHtmlVariable(index + 1, variable));
                    })

                }
                reader.readAsText($("#fileUpload")[0].files[0]);
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid XML file.");
        }
    }

    drawVariables(e) {
        var data_apps = JSON.parse(e);

        $(data_apps).each(function (index, variable) {
            $("#variablesXApp").append(c_Variable.loadHtmlVariable(index + 1, variable));

        })

        $("#modal_variables").modal("toggle");
    }

    loadHtmlVariable(varConsecutivo, itemVariable) {
        var sel_option = "";
        var objvalue = ["java.lang.String", "java.lang.Boolean"];
        $(objvalue).each(function (index, itemValue) {
            var textValue = itemValue.replace("java.lang.", "");
            if (itemValue == itemVariable.tipo) {
                sel_option = sel_option + `<option value="${itemValue}" selected>${textValue}</option>`;
            }
            else {
                sel_option = sel_option + `<option value="${itemValue}">${textValue}</option>`;
            }
        })


        var varHtml = `<div class="row inp_var${varConsecutivo} countVariables" style="margin-top: 1vh;">
                    <div class="col-sm-12 col-md-12 col-lg-3">
                      <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-md-12">
                          <input type="text" class="form-control inp_var_nombre" placeholder="Ingrese el nombre." value="${itemVariable.nombre}">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-3">
                      <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-md-12">
                          <input type="text" class="form-control inp_var_valor" placeholder="Ingrese el Valor." value="${itemVariable.valor}">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-3">
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-md-12">
                                <input type="text" class="form-control inp_var_descripcion" placeholder="Descripción" value="${itemVariable.descripcion}">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-3">
                        <div class="input-group">
                            <!-- <input type="text" class="form-control inp_var_tipo" placeholder="Tipo" selected="${itemVariable.tipo}"> -->
                            <select class="form-control inp_var_tipo" placeholder="Tipo">
                            ${sel_option}
                            </select>
                            <span class="input-group-addon" onclick=' document.getElementsByClassName(" inp_var${varConsecutivo}")[0].remove(); '>
                            <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"> </i>
                            </span>
                        </div>
                    </div>
                  </div>
                </div>`;

        $("#txt_nombreApp").text(itemVariable.nombreApp);
        $("#modal_idApp").data('idApp', itemVariable.idAPP);

        return varHtml;
    }
}




let c_Variable = new C_Variable();
