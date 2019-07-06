class App {
  // Constructor
  constructor(id, idResponsable, nombre, ambiente, plataforma, variables) {
    this.id = id || null;
    this.idResponsable = idResponsable || null;
    this.nombre = nombre || null;
    this.ambiente = ambiente || null;
    this.plataforma = plataforma || null;
    this.variables = variables || null;

  }

  //Getter
  get ReadAll() {
    var miAccion = 'ReadAll';

    $.ajax({
      type: "POST",
      url: "class/App.php",
      data: {
        action: miAccion,
        obj: JSON.stringify(app)
      }
    })
      .done(function (e) {
        app.drawApp(e);
      })
      .fail(function (e) {
        // app.showError(e);
      });
  }

  get validaFormNewApp() {
    app.nombre = $("#inp_newAppName").val();
    app.ambiente = $("#sel_newAppAmbiente").val();
    app.plataforma = $("#sel_newAppPlataforma").val();

    return app.nombre.length > 3 ? true : false;
    // if (app.nombre.length < 3){
    //   return false;
    // }      
    // else return
  }

  get Create() {
    var miAccion = 'Create';
    if (app.validaFormNewApp) {
      $.ajax({
        type: "POST",
        url: "class/App.php",
        data: {
          action: miAccion,
          obj: JSON.stringify(app)
        }
      })
        .done(function (e) {
          app.ReadAll;
        })
        .fail(function (e) {
          // app.showError(e);
        });
    }
  }

  get Clear() {
    $("#inp_newAppName").val("");
  }

  get Delete() {
    var miAccion = 'Delete';
    $.ajax({
      type: "POST",
      url: "class/App.php",
      data: {
        action: miAccion,
        obj: JSON.stringify(app)
      }
    })
      .done(function (e) {
        app.ReadAll;
      })
      .fail(function (e) {
        // app.showError(e);
      });
  }

  get Make() {
    $("#modal_ear").modal("toggle");
  }

  get Edit() {
    app.Clear;

    var miAccion = 'ReadAppByid';

    $.ajax({
      type: "POST",
      url: "class/App.php",
      data: {
        action: miAccion,
        obj: JSON.stringify(app)
      }
    })
      .done(function (e) {
        app.EditApp(e);
      })
      .fail(function (e) {
        // app.showError(e);
      });
  }

  get Update() {
    var miAccion = 'Update';
    if (app.validaFormNewApp) {
      $.ajax({
        type: "POST",
        url: "class/App.php",
        data: {
          action: miAccion,
          obj: JSON.stringify(app)
        }
      })
        .done(function (e) {
          app.ReadAll;
        })
        .fail(function (e) {
          // app.showError(e);
        });
    }
  }

  drawApp(e) {
    var data_apps = JSON.parse(e);

    var tbl_apps = $('#tbl_apps').DataTable({
      data: data_apps,
      // responsive: true,
      destroy: true,
      responsive: true,
      info: false,
      searching: false,
      // scrollX: false,
      // scrollY: false,
      // scrollCollapse: true,
      language: {
        "infoEmpty": "Sin Apps Seleccionadas",
        "emptyTable": "Sin Apps Seleccionadas",
        "search": "Buscar",
        "zeroRecords": "No hay resultados",
        "lengthMenu": "Mostrar _MENU_ registros",
        "paginate": {
          "first": "Primera",
          "last": "Ultima",
          "next": "Siguiente",
          "previous": "Anterior"
        }
      },
      columnDefs: [
        {
          title: "idAPP",
          data: "id",
          targets: 0,
          visible: false
        },
        {
          title: "Nombre",
          data: "nombre",
          targets: 1
        },
        {
          title: "idResponsable",
          data: "idResponsable",
          targets: 2,
          visible: false
        },
        {
          title: "Responsable",
          data: "responsable",
          targets: 3
        },
        {
          title: "Ambiente",
          data: "ambiente",
          targets: 4
        },
        {
          title: "Plataforma",
          data: "plataforma",
          targets: 5
        },
        {
          title: "Preparar EAR",
          targets: 6,
          visible: true,
          width: "10%",
          mRender: function (e) {
            return `<button class=btnEliminarApp onclick="handlerApp(this, 'make')" > <i class="fa fa-upload" style="color:forestgreen" aria-hidden="true"></i> Preparar</button>`;
          }
        },
        {
          title: "Editar",
          targets: 7,
          visible: true,
          width: "10%",
          mRender: function (e) {
            return `<button class=btnEliminarApp onclick="handlerApp(this, 'delete')" > <i class="fa fa-trash-o" style="color:firebrick" aria-hidden="true"></i> Eliminar</button>`;
          }
        },
        {
          title: "Eliminar",
          targets: 8,
          visible: true,
          width: "10%",
          mRender: function (e) {
            return `<button class=btnEditarApp onclick="handlerApp(this, 'edit')" > <i class="fa fa-pencil" style="color:#3F51B5" aria-hidden="true"></i> Editar</button>`;
          }
        }
      ],
      order: [
        [1, "asc"]
      ]
    });


  }

  EditApp(e) {
    var data_apps = JSON.parse(e);


    $("#modal_new_APP").data('idApp', data_apps[0].id);

    $("#inp_newAppName").val(data_apps[0].nombre);
    $("#sel_newAppAmbiente").val(data_apps[0].ambiente);
    $("#sel_newAppPlataforma").val(data_apps[0].plataforma);

    $("#modal_new_APP").modal("toggle");
  }


}

let app = new App();
