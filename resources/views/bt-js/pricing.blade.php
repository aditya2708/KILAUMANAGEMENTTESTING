<script>
    function entryLevelForm() {
        return {
            addLevelModal: false,
            params: {
                data:{
                    level:{
                        jumlah_level: '',
                    },
                    listApk:{
                        jumlah_apk: '',
                    },
                }
            },

            openModal(data = null) {
                if (data) {
                    this.params = { ...data };
                } else {
                    this.params = { id: null, level: '', karyawan: '', harga: '' };
                }
                this.addLevelModal = true;
            },
            list: @js($data),
            listApk: @js($dataApk),
            listCompany: @js($dataCompany),
            listTrial: @js($dataTrial),
            newCompany: {
                company: '',
                harga: ''
            },
    
            async submitForm() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
                try {
                    const response = await fetch("{{ route('entry-level') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ 
                            data: {
                                level: this.list,
                                listApk: this.listApk,
                                listCompany: this.listCompany,
                                newCompany: this.newCompany,
                                listTrial: this.listTrial
                            } 
                        })
                    });
    
                    const result = await response.json();
    
                    if (result.status) {
                        showMessage(result.message, 'success');
    
                       // Kosongkan dulu agar Alpine tahu ada perubahan
                        this.list = [];
                        this.listApk = [];
                        
                        await new Promise(r => setTimeout(r, 10)); // Delay singkat supaya Alpine flush
                        
                        // Assign ulang data baru
                        this.list = result.data.map(item => ({ ...item }));
                        this.listApk = result.dataApk.map(item => ({ ...item }));
                        
                        console.log("List di-reset:", this.list);

                    } else {
                        showMessage(result.message || 'Gagal menyimpan data.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                }
            },
            
            
            async saveLevel() {
                try {
                    const response = await fetch("{{route('entry-level')}}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.params)
                    });

                    const result = await response.json();
                    console.log(result)
                    if (result.status) {
                        showMessage(result.message, 'success');
                        this.addLevelModal = false;
                        // Kosongkan dulu agar Alpine tahu ada perubahan
                        this.list = [];
                        this.listApk = [];
                        
                        await new Promise(r => setTimeout(r, 10)); // Delay singkat supaya Alpine flush
                        
                        // Assign ulang data baru
                        this.list = result.data.map(item => ({ ...item }));
                        this.listApk = result.dataApk.map(item => ({ ...item }));
                        


                    } else {
                        showMessage(result.message || 'Gagal menyimpan data.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    showMessage('Terjadi kesalahan saat mengirim data.', 'error');
                }
            },
            
            async updateStatus(status) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
                fetch('auto-aktivasi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        status: status ? 1 : 0 // true/false menjadi 1/0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showMessage('Status berhasil diperbarui:');
                })
                .catch(error => {
                    showMessage('Gagal memperbarui status:', 'error');
                });
            }
        }
    }
    
    

    const showMessage = (msg = '', type = 'success') => {
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
    };
</script>
