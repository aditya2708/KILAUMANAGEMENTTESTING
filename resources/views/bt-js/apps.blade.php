<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('apps', () => ({
        datatable: null,
        isShowNoteMenu: false,
        isAddNoteModal: false,
        isShowTaskMenu: false,
        selectedTab: '',
        listApk: [],
        defaultApp: {
            id: null,
            name: '',
            des: '',
            foto: '',
            namafile_foto: '',
            typee: '',
            urllogin: '',
            jenis: ''
        },
        params: {
            id: null,
            name: '',
            des: '',
            foto: '',
            namafile_foto: '',
            typee: '',
            urllogin: '',
            jenis: ''
        },
        previewUrl: '', // For image preview
        showImage: false,
        init() {
            this.loadData()
            this.tombol()
        },
        
        encodeImageFileAsURL(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.previewUrl = URL.createObjectURL(file);
            this.showImage = true;

            const reader = new FileReader();
            reader.onloadend = () => {
                this.params.foto = reader.result;
                this.params.namafile_foto = file.name;// Store base64 image in params.foto
            };
            reader.readAsDataURL(file);
        },
        
        loadData(){
            fetch("{{ url('berbagi-teknologi/getapps') }}")
                .then(response => response.json())
                .then(apiData => {
                    
                    if (this.datatable) {
                        this.datatable.destroy();
                    }
                    
                    const tableData = apiData.data.map((row, index) => [
                        index + 1, // Nomor urut
                        ...row,
                        this.actionButtons(row[0]) // Append action buttons to each row
                    ]);
                    
                    this.datatable = new simpleDatatables.DataTable('#myTable', {
                        data: {
                            headings: [...apiData.headings, 'Aksi'],
                            data: tableData
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [
                            {
                                select: 0,
                                sort: 'asc',
                            },
                            
                            {
                                select: 1,
                                hidden: true,
                            },
                            
                            {
                                select: 2,
                                hidden: true,
                            },
                            
                            {
                                select: 3,
                                render: (data, cell, row) => {
                                    return `<div class="flex items-center gap-2">
                                                <img src="{{ url('kilau/upload') }}/${row[2]}" class="w-9 h-9 rounded-full max-w-none" alt="user-profile" />
                                            <div class="font-semibold">${data}</div>
                                    </div>`;
                                },
                            },
                            
                            {
                                select: apiData.headings.length,
                                sortable: false,
                            },
                        ],
                        firstLast: true,
                        firstText:
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        lastText:
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        prevText:
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        nextText:
                            '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                        labels: {
                            perPage: '{select}',
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}',
                        },
                    });
                    // this.datatable.page(currentPage - 1);
            })
        },
        
        actionButtons(id) {
            return `<div class="flex gap-4">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="${id}">
                            Edit
                        </button>
                    </div>`;
        },
        
        tombol(){
            document.addEventListener('click', (event) => {
                if (event.target.matches('.edit-btn')) {
                    const id = event.target.dataset.id;
                    this.add(id);
                } else if (event.target.matches('.delete-btn')) {
                    const id = event.target.dataset.id;
                    this.deleteUser(id);
                }
            });
        },
        
        add(info) {
            
            this.isAddNoteModal = true;
            this.previewUrl = '';
            this.params = this.defaultApp;
            this.showImage = false;
            
            if (info) {
                fetch("{{ url('berbagi-teknologi/editapps') }}/" + info, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(dataNya => {
                        var datay = dataNya.data;
                        this.params = {
                            id: datay.id,
                            name: datay.aplikasi,
                            des: datay.deskripsi,
                            typee: datay.type,
                            urllogin: datay.link,
                            namafile_foto: datay.logo,
                            jenis: datay.jenis,
                        }
                        if (datay.logo) {
                            this.previewUrl = "{{ url('kilau/upload') }}/" + datay.logo;
                            this.showImage = true;
                        }
                    })
                
            }
        },
        
        deleteUser(id) {
                
            const swalWithBootstrapButtons = window.Swal.mixin({
                confirmButtonClass: 'btn btn-secondary',
                cancelButtonClass: 'btn btn-dark ltr:mr-3 rtl:ml-3',
                buttonsStyling: false,
                customClass: 'sweet-alerts',
            });
            swalWithBootstrapButtons
            .fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true,
                padding: '2em',
                customClass: 'sweet-alerts',
            })
            .then((result) => {
                if (result.value) {
                    const currentPage = this.datatable.currentPage + 1;
                    fetch("{{ url('berbagi-teknologi/delapps') }}/" + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data)
                        this.showMessage('Your file has been deleted');
                        this.loadData(currentPage);
                    })
                } else if (result.dismiss === window.Swal.DismissReason.cancel) {
                    this.showMessage('Your imaginary file is safe :)','error');
                }
            });
        },
        
        saveApp() {
            if (!this.params.name) {
                this.showMessage('Nama Aplikasi is required.', 'error');
                return true;
            }
            
            if (!this.params.des) {
                this.showMessage('Deskripsi is required.', 'error');
                return true;
            }
            
            if (!this.params.foto) {
                this.showMessage('Foto is required.', 'error');
                return true;
            }
                
            fetch("{{ url('berbagi-teknologi/postapps') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(this.params),
            })
            .then(response => response.json())
            .then(data => {
                // const currentPage = this.datatable.currentPage + 1;
                
                this.params = this.defaultApp;
                this.previewUrl = '';
                this.showImage = false;
                
                this.showMessage(data.message);
                this.isAddNoteModal = false;
                // this.loadData(currentPage)
                 this.loadData()
                        
            })
            .catch(error => {
                this.showMessage(`Error: ${error}`, 'error');
            });
        },
            
        showMessage(msg = '', type = 'success') {
            const toast = window.Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
            });
            toast.fire({
                icon: type,
                title: msg,
                padding: '10px 20px',
            });
        },
    })); 
});
</script>