<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('users', () => ({
            datatable: null,
            addUserModal: false,
            defaultParams: { id: null, nama: '', email: '', cmskilau: '' },
            params: {
                id: null,
                nama: '',
                email: '',
                cmskilau: ''
            },
            init() {
                this.loadData()
                this.tombol()
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
            
            loadData(){
                fetch("{{ url('berbagi-teknologi/getusers') }}")
                    .then(response => response.json())
                    .then(apiData => {
                        
                        if (this.datatable) {
                            this.datatable.destroy();
                        }
                        
                        const tableData = apiData.data.map((row, index) => [
                            index + 1,
                            ...row,
                            this.hakAkses(row[0]),
                            this.aktifButton(row[0], 1)
                        ]);
                        
                        this.datatable = new simpleDatatables.DataTable('#myTable', {
                            data: {
                                headings: ['No.', ...apiData.headings, 'Hak Akses', 'Aktif'],
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
                                    select: [4,5],
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
                    if (event.target.matches('.showmodal')) {
                        // console.log(event.target)
                        const id = event.target.dataset.id;
                        this.kasihAkses(id);
                        
                    }
                });
            },
            
            updateStatus(id, status) {
                fetch(`{{ url('berbagi-teknologi/updateStatus') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ id, status }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Status updated successfully');
                        } else {
                            console.error('Failed to update status');
                        }
                    });
            },
            
            aktifButton(id, datas) {
                return `<label class="w-12 h-6 relative">
                        <input type="checkbox" class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer" ${datas === 1 ? 'checked' : ''} @change="$store.users.updateStatus(${id}, $event.target.checked ? 1 : 0)"/>
                        <span for="custom_switch_checkbox2" class="outline_checkbox bg-icon border-2 border-[#ebedf2] dark:border-white-dark block h-full rounded-full before:absolute before:left-1 before:bg-[#ebedf2] dark:before:bg-white-dark before:bottom-1 before:w-4 before:h-4 before:rounded-full before:bg-[url('../images/close.svg')] before:bg-no-repeat before:bg-center peer-checked:before:left-7 peer-checked:before:bg-[url('../images/checked.svg')] peer-checked:border-primary peer-checked:before:bg-primary before:transition-all before:duration-300"></span>
                    </label>`;
            },
            
            hakAkses(id) {
                return `
                <div class="flex items-left gap-4">
                    <a href="javascript:;" class="btn btn-sm btn-secondary showmodal gap-2" data-id="${id}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Hak Akses
                    </a>
                </div>
                `;
            },
            
            kasihAkses(id) {
                this.addUserModal = true;
                
                this.params = this.defaultParams;
            
                if (id) {
                    fetch("{{ url('berbagi-teknologi/getUserById') }}/" + id, {
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
                                nama: datay.nama,
                                email: datay.email,
                                cmskilau: datay.kilau
                            }
                        })
                    
                }
                
                // console.log(id)
            },
            
            saveUsers(){
                
                    
                fetch("{{ url('berbagi-teknologi/postUserAkses') }}", {
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
                    
                    this.params = this.defaultParams;
                    
                    this.showMessage(data.message);
                    this.addUserModal = false;
                    this.loadData()
                            
                })
                .catch(error => {
                    this.showMessage(`Error: ${error}`, 'error');
                });
            }
        })); 
    })
</script>