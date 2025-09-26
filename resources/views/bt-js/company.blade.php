<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pagination', () => ({
            addContactModal: false,
            datatable: null,
            params: {
                id: null,
                nama: '',
            },
            
            init() {
                this.loadData()
                this.tombol()
            },
            
            loadData(){
                fetch("{{ url('berbagi-teknologi/getcompany') }}")
                .then(response => response.json())
                .then(apiData => {
                    
                    if (this.datatable) {
                        this.datatable.destroy();
                    }
                    
                    this.datatable = new simpleDatatables.DataTable('#myTable1', {
                        data: {
                            headings: ['No.', ...apiData.headings.slice(0, 6), 'Aksi', ...apiData.headings.slice(7)],
                            data: apiData.data.map((row, index) => [
                                index + 1,
                                ...row.slice(0, 6),
                                this.actionButtons(row[0]),
                                this.aktifButton(row[0], ...row.slice(7)),
                            ]),
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
                                select: 7,
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
            
            tombol(){
                document.addEventListener('click', (event) => {
                    if (event.target.matches('.edit-btn')) {
                        const id = event.target.dataset.id;
                        this.editUser(id);
                    } else if (event.target.matches('.delete-btn')) {
                        const id = event.target.dataset.id;
                        this.deleteUser(id);
                    }
                });
                
                document.addEventListener("change", (event) => {
                    if (event.target.classList.contains("custom_switch")) {
                        let switchElement = event.target;
                        let id = switchElement.getAttribute("data-id");
                        let previousStatus = switchElement.checked ? 0 : 1; // Simpan status sebelumnya
                        let newStatus = switchElement.checked ? 1 : 0; // Status yang akan diubah
                        
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
                                confirmButtonText: 'Yes, Do it!',
                                cancelButtonText: 'No, cancel!',
                                reverseButtons: true,
                                padding: '2em',
                                customClass: 'sweet-alerts',
                            })
                            .then((result) => {
                                if (result.value) {
                                    // Jika konfirmasi, jalankan fungsi updateStatus
                                    this.updateStatus(id, newStatus);
                                } else if (result.dismiss === window.Swal.DismissReason.cancel) {
                                    // Jika dibatalkan, kembalikan switch ke keadaan semula
                                    switchElement.checked = previousStatus === 1;
                                    this.showMessage('Your Data is safe :)', 'error');
                                }
                            });
                    }
                });
            },
            
            add() {
                this.addContactModal = true;
            },
            
            actionButtons(id) {
                return `<div class="flex gap-4">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="${id}">
                                Lihat
                            </button>
                        </div>`;
            },
            
            aktifButton(id, datas) {
                return `<label class="w-12 h-6 relative">
                        <input type="checkbox" class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer" ${datas === 1 ? 'checked' : ''}
                            data-id="${id}"
                            data-status="${datas}" />
                        <span for="custom_switch_checkbox2" class="outline_checkbox bg-icon border-2 border-[#ebedf2] dark:border-white-dark block h-full rounded-full before:absolute before:left-1 before:bg-[#ebedf2] dark:before:bg-white-dark before:bottom-1 before:w-4 before:h-4 before:rounded-full before:bg-[url('../images/close.svg')] before:bg-no-repeat before:bg-center peer-checked:before:left-7 peer-checked:before:bg-[url('../images/checked.svg')] peer-checked:border-primary peer-checked:before:bg-primary before:transition-all before:duration-300"></span>
                    </label>`;
            },
            
            editUser(id) {
                alert(`Edit user with ID: ${id}`);
            },
            
            updateStatus(id, status) {
                fetch(`{{ url('berbagi-teknologi/AktifCompany') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ id, status }),
                })
                .then(response => response.json())
                .then(data => {
                        this.showMessage(data.message);
                });
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
                        fetch("{{ url('berbagi-teknologi/delcompany') }}/" + id, {
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
                            this.loadData();
                        })
                    } else if (result.dismiss === window.Swal.DismissReason.cancel) {
                        this.showMessage('Your imaginary file is safe :)','error');
                    }
                });
            },
            
            saveUser() {
                if (!this.params.name) {
                    this.showMessage('Name is required.', 'error');
                    return true;
                }
                
                fetch("{{ url('berbagi-teknologi/postcompany') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.params),
                })
                .then(response => response.json())
                .then(data => {
                    
                    this.params = {
                        name: '',
                    };
                    
                    this.showMessage(data.message);
                    this.addContactModal = false;
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
    })
</script>