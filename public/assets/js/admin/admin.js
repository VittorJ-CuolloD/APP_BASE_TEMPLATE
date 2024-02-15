function highlightMenu(element) {
    document.querySelectorAll(".nav-link").forEach(link => {
        if (link.textContent === element) {
            link.classList.add("active");
        }
    });
}

function generarPassword(longitud) {
    var caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var password = '';

    for (var i = 0; i < longitud; i++) {
      var indice = Math.floor(Math.random() * caracteres.length);
      password += caracteres.charAt(indice);
    }

    return password;
}

function createPassword(event){

    return document.querySelector(".form-icon-trailing").value = generarPassword(8)

}

function changeTypePassword(event){

    console.log(document.querySelectorAll(".form-icon-trailing i"));

    var tipo = document.querySelector(".form-icon-trailing").type

    document.querySelector(".form-icon-trailing").type = tipo ==='text' ? 'password' : 'text'

}

function MDBForm(id) {
    let form = document.getElementById(id);
    if (form != null) {
        form.parentNode.classList.add("form-box")
        form.querySelectorAll("div").forEach(div => {
            if (div != null && div.querySelector("input") != null && div.querySelector("label") != null) {
                div.className = "form-outline my-4";
                div.querySelector("input").className = "form-control";
                div.querySelector("label").className = "form-label";
                div.insertBefore(div.querySelector("input"), div.querySelector("label"));
            }
        });
        if (form.parentNode.querySelector("button") != null) {
            form.parentNode.querySelector("button").classList.add("submit-btn")
        }
    }
}

function loadResources() {
    if (document.querySelector('.form-outline') != null) {
        document.querySelectorAll('.form-outline').forEach((form) => {
            new mdb.Input(form);
        });

        if (document.querySelector('.select') != null)
        document.querySelectorAll('.select').forEach((select) => {
            new mdb.Select(select,{searchPlaceholder:window.search+'...'});
        });
    }
    if (document.querySelector("#datatable") != null) {
        let datatable = document.querySelector("#datatable");

        const instance = new mdb.Datatable(datatable,{},{maxWidth:"100%",sm:true, hover: true, striped: true, fullPagination: true, fixedHeader: true});

        let div = document.createElement("div");
        div.className = "d-flex justify-content-end align-items-center w-100";
        div.innerHTML = 
            "          <div class='col-12 col-sm-6 col-md-3 col-lg-2'>"+
            "               <div class=' input-group datatable-search my-2'>" +
                "                <input type='text' class='form-control' id='advanced-search-input' placeholder='"+window.search+"...'/>" +
                "                <button class='btn btn-success' id='advanced-search-button' type='button'>" +
                "                    <i class='fa fa-search'></i>" +
                "                </button>" +
            "               </div>"+
            "           </div>";
        datatable.parentNode.insertBefore(div, datatable);

        const searchInput = document.getElementById('advanced-search-input');
        const search = (value) => {
            let [phrase, columns] = value.split(' in:').map((str) => str.trim());
            if (columns) {
                columns = columns.split(',').map((str) => str.toLowerCase().trim());
            }
            instance.search(phrase, columns);
        }
        document.getElementById('advanced-search-button').addEventListener('click', (e) => {
            search(searchInput.value)
        });
        searchInput.addEventListener('keydown', (e) => {
            search(e.target.value);
        });

        if (document.querySelectorAll("th") != null) {
            document.querySelectorAll("th").forEach(th => {
                th.classList.add("h6")
            });
        }

        if (document.querySelectorAll("form") != null) {
            document.querySelectorAll("form").forEach(form => {
                console.log(document.querySelectorAll("form").length);
                form.addEventListener("submit", deleteForm, false);
            });
        }
    }
}

function deleteForm(ev) {
    ev.preventDefault();
    Swal.fire({
        icon: 'warning',
        title: confirmation,
        text: are_you_sure_to_delete_this_item,
        confirmButtonText: si,
        showCancelButton: true,
        cancelButtonText: no,
        customClass: {
            confirmButton: 'btn btn-success gap-5',
            cancelButton: 'btn btn-danger',
            actions:'gap-4'
          },
          buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            ev.target.submit();
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadResources();
}, false);