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

function createPassword(id){

    var password = generarPassword(8)

    document.querySelector(".form-icon-trailing").value = password
    document.getElementById(id).value = password

}

function changeTypePassword(id){

    var tipo = document.getElementById(id).type;

    document.getElementById(id).type = tipo ==='text' ? 'password' : 'text'

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
        document.querySelectorAll('.select').forEach((select) => {
            new mdb.Select(select);
        });
    }
    if (document.querySelector("#datatable") != null) {
        let datatable = document.querySelector("#datatable");
        const instance = new mdb.Datatable(datatable);

        let div = document.createElement("div");
        div.className = "d-flex justify-content-end";
        div.innerHTML = "<div class='input-group mb-4 datatable-search'>" +
            "                <input type='text' class='form-control' id='advanced-search-input' placeholder='Buscar'/>" +
            "                <button class='btn btn-primary' id='advanced-search-button' type='button'>" +
            "                    <i class='fa fa-search'></i>" +
            "                </button>" +
            "            </div>";
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
                th.classList.add("th-sm");
            });
        }

        if (document.querySelectorAll("form") != null) {
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", deleteForm, false);
            });
        }
    }
}

function deleteForm(ev) {
    ev.preventDefault();
    Swal.fire({
        icon: 'warning',
        title: 'Confirmación',
        text: '¿Está seguro de eliminar este elemento?',
        confirmButtonColor: "#9f0303",
        confirmButtonText: "¡Sí, eliminar!",
        showCancelButton: true,
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            ev.target.submit();
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadResources();
}, false);