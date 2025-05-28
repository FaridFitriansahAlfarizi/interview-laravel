<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100 p-6" x-data="productApp()" x-init="init()">

    <div class="container mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow px-6 py-4 mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Daftar Produk</h1>
        </div>

        <!-- Kontainer utama untuk pencarian dan daftar produk -->
        <div class="bg-white rounded-xl shadow p-6">
            <!-- Top Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <!-- Input Cari -->
                <div class="relative w-full md:w-1/3">
                    <input type="text" placeholder="Cari produk" x-model="search" @input="filterProducts()"
                        class="w-full pl-4 pr-10 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Tombol Tambah Produk -->
                <div class="flex flex-wrap gap-2">
                    <button @click="openAddModal()"
                        class="w-full md:w-auto bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-full text-sm">
                        Tambah Produk
                    </button>
                </div>
            </div>

            <!-- Produk Grid -->
            <div id="product-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="product in paginatedProducts()" :key="product.id">
                    <div
                        class="rounded-xl shadow bg-white overflow-hidden relative flex flex-col border border-gray-200 hover:shadow-lg transition">
                        <div class="bg-gray-100 flex justify-center items-center h-32">
                            <img :src="product.image_url || 'https://placehold.co/150x150'" :alt="product.name"
                                class="h-20 object-contain" />
                        </div>
                        <div class="p-4 flex flex-col gap-1 w-full text-left">
                            <h2 class="text-sm font-bold text-gray-800" x-text="product.name"></h2>
                            <p class="text-lg text-purple-600 font-bold text-sm">Rp <span
                                    x-text="product.price.toLocaleString()"></span></p>
                            <div class="flex justify-between items-center mt-4 gap-2">
                                <button @click="deleteProduct(product.id)"
                                    class="p-2 rounded-full bg-white text-red-500 border border-gray-300 hover:bg-gray-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                    </svg>
                                </button>
                                <button @click="openEditModal(product.id)"
                                    class="w-full h-10 text-sm bg-gray-100 text-purple-600 px-4 rounded-full hover:bg-gray-200 transition">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination Controls -->
            <div class="mt-6 flex justify-center items-center gap-2" x-show="totalPages() > 1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-3 py-1 text-sm rounded-full border"
                    :class="currentPage === 1 ? 'text-gray-400 border-gray-200' :
                        'text-gray-700 hover:bg-gray-100 border-gray-300'">
                    &laquo;
                </button>

                <template x-for="page in totalPages()" :key="page">
                    <button @click="changePage(page)" class="px-3 py-1 text-sm rounded-full border"
                        :class="page === currentPage ? 'bg-purple-600 text-white border-purple-600' :
                            'text-gray-700 hover:bg-gray-100 border-gray-300'">
                        <span x-text="page"></span>
                    </button>
                </template>

                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages()"
                    class="px-3 py-1 text-sm rounded-full border"
                    :class="currentPage === totalPages() ? 'text-gray-400 border-gray-200' :
                        'text-gray-700 hover:bg-gray-100 border-gray-300'">
                    &raquo;
                </button>
            </div>

            <!-- Modal Add/Edit Produk -->
            <div x-show="modalOpen" x-transition class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
                style="display:none;">
                <div @click.away="closeModal()" x-show="modalOpen" x-transition
                    class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 relative">
                    <button @click="closeModal()"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">&times;</button>

                    <h2 class="text-lg font-semibold mb-4"
                        x-text="editingProduct.id ? 'Edit Data Produk' : 'Tambah Data Produk'"></h2>

                    <form @submit.prevent="submitForm" enctype="multipart/form-data" id="product-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload gambar produk</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full border rounded-lg px-3 py-2 bg-white" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                <input type="text" name="name" x-model="editingProduct.name"
                                    class="w-full border rounded-lg px-3 py-2" required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Stok</label>
                                <input type="number" name="stock" x-model.number="editingProduct.stock"
                                    class="w-full border rounded-lg px-3 py-2" required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                                <input type="number" name="price" x-model.number="editingProduct.price"
                                    class="w-full border rounded-lg px-3 py-2" required />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" rows="2" x-model="editingProduct.description"
                                    class="w-full border rounded-lg px-3 py-2"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 text-right">
                            <button type="submit"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-full text-sm"
                                x-text="editingProduct.id ? 'Simpan Perubahan' : 'Tambah'"></button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function productApp() {
            return {
                products: [],
                filteredProducts: [],
                search: '',
                modalOpen: false,
                currentPage: 1,
                itemsPerPage: 4,
                editingProduct: {
                    id: null,
                    name: '',
                    stock: '',
                    price: '',
                    description: '',
                    image_url: ''
                },

                async fetchProducts() {
                    try {
                        const res = await fetch('/api/products');
                        if (!res.ok) throw new Error('Failed to fetch products');
                        const data = await res.json();
                        this.products = data.data;
                        this.filteredProducts = this.products;
                    } catch (err) {
                        console.error(err);
                        alert('Gagal mengambil data produk.');
                    }
                },

                filterProducts() {
                    const s = this.search.toLowerCase();
                    this.filteredProducts = this.products.filter(p => p.name.toLowerCase().includes(s));
                    this.currentPage = 1;
                },

                paginatedProducts() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredProducts.slice(start, end);
                },

                totalPages() {
                    return Math.ceil(this.filteredProducts.length / this.itemsPerPage);
                },

                changePage(page) {
                    if (page >= 1 && page <= this.totalPages()) {
                        this.currentPage = page;
                    }
                },

                openAddModal() {
                    this.resetForm();
                    this.modalOpen = true;
                },

                async openEditModal(id) {
                    try {
                        const res = await fetch(`/api/products/${id}`);
                        if (!res.ok) throw new Error('Gagal memuat data produk');
                        const data = await res.json();
                        const product = data.data;
                        this.editingProduct = {
                            ...product
                        };
                        this.modalOpen = true;
                    } catch (err) {
                        console.error(err);
                        alert('Gagal memuat data produk.');
                    }
                },

                closeModal() {
                    this.modalOpen = false;
                    this.resetForm();
                },

                resetForm() {
                    this.editingProduct = {
                        id: null,
                        name: '',
                        stock: '',
                        price: '',
                        description: '',
                        image_url: ''
                    };
                    const fileInput = document.querySelector('input[name="image"]');
                    if (fileInput) fileInput.value = '';
                },

                async submitForm(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    const isEdit = !!this.editingProduct.id;
                    const url = isEdit ? `/api/products/${this.editingProduct.id}` : '/api/products';

                    if (isEdit) {
                        formData.append('_method', 'PUT');
                    }

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        if (res.ok) {
                            alert(isEdit ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.');
                            this.closeModal();
                            await this.fetchProducts();
                            this.filterProducts();
                        } else {
                            const error = await res.json();
                            alert(error.message || 'Gagal menyimpan produk.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan saat menyimpan produk.');
                    }
                },

                async deleteProduct(id) {
                    if (!confirm('Yakin ingin menghapus produk ini?')) return;
                    try {
                        const res = await fetch(`/api/products/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (res.ok) {
                            alert('Produk berhasil dihapus.');
                            await this.fetchProducts();
                            this.filterProducts();
                        } else {
                            alert('Gagal menghapus produk.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan saat menghapus produk.');
                    }
                },

                init() {
                    this.fetchProducts();
                }
            }
        }
    </script>
</body>

</html>
