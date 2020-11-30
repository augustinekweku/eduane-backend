<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
                <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                    <p class="_title0">
                        Meal Plan
                        <Button @click="addModal = true" icon="md-add">Add Meal Plan</Button>
                    </p>

                    <div class="_overflow _table_div">
                        <table class="_table unbordered-table">
                            <!-- TABLE TITLE -->
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>description</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            <!-- TABLE TITLE -->
                            
                                                        <!-- ITEMS -->
                            <tr v-for="(plan, i) in mealPlans" :key="i" v-if="mealPlans.length">
                                <td>{{plan.id}}</td>
                                <td class="table_image">
                                    <img height="40px" width="50px" :src="plan.featuredImage" />
                                </td>
                                <td class="_table_name">{{plan.title}}</td>
                                <td >{{plan.description}}</td>
                                <td class="">{{plan.created_at}}</td>
                                <td>
                                    <Button size="small" shape="circle"  @click="showViewModal(plan, i)"><Icon type="md-eye" /></Button>                                    
                                    <Button size="small" type="info" @click="showEditModal(plan, i)">Edit</Button>
                                    <Button size="small" type="error">Delete</Button>
                                </td>
                            </tr>
                            <!-- ITEMS -->
                        </table>
                    </div>
                </div>

                <!-- meal plan adding modal -->
                <Modal :closable="false" :mask-closable="false" title="Add Meal Plan" v-model="addModal">
                    <Input v-model="data.title" placeholder="Title"/>
                    <div class="space"></div>
                    <Input v-model="data.description" placeholder="Description"/>
                    <div class="space"></div>
                    <div class="row">

                        <div class="col-3">
                            <div class="row">
                                <div class="col-1" style="display:flex; align-items:center;">
                                    <p style="font-weight:bold; font-size:17px;">≤</p>
                                </div>
                                <div class="col-9" style="margin-left:-15px">
                                    <Input v-model="data.calories" placeholder="Calories"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p>kcal</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="data.protein" placeholder="Protein"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>

                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="data.fat" placeholder="Fat"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="data.carbs" placeholder="Carbs"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="space"></div>
                    <div class="space"></div>
                    <h2>Featured Image</h2>
                    <Upload :format="['jpg', 'jpeg', 'png']" :headers="{
                                                    'x-csrf-token': token,
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }" :max-size="2048" :on-error="handleError"
                                                :on-exceeded-size="handleMaxSize"
                                                :on-format-error="handleFormatError" 
                                                :on-success="handleSuccess" action="/app/upload" ref="uploads" type="drag">
                        <div style="padding: 20px 0">
                            <Icon size="52" style="color: #3399ff" type="ios-cloud-upload"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>

                    <div class="demo-upload-list" v-if="data.featuredImage">
                        <img :src="`${data.featuredImage}`"/>

                        <div class="demo-upload-list-cover">
                            <Icon @click="deleteImage" type="ios-trash-outline"></Icon>
                        </div>
                    </div>

                    <div slot="footer">
                        <Button @click="addModal = false" type="error">Close</Button>
                        <Button :disabled="isAdding" :loading="isAdding" @click="addMealPlan" type="primary">
                            {{ isAdding ? "Adding..." : "Add Category" }}
                        </Button>
                    </div>
                </Modal>

                <!-- meal plan editing modal -->
                <Modal :closable="false" :mask-closable="false" title="Edit Meal Plan" v-model="editModal">
                    <Input v-model="editData.title" placeholder="Title"/>
                    <div class="space"></div>
                    <Input v-model="editData.description" placeholder="Description"/>
                    <div class="space"></div>
                    <div class="row">

                        <div class="col-3">
                            <div class="row">
                                <div class="col-1" style="display:flex; align-items:center;">
                                    <p style="font-weight:bold; font-size:17px;">≤</p>
                                </div>
                                <div class="col-9" style="margin-left:-15px">
                                    <Input v-model="editData.calories" placeholder="Calories"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p>kcal</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="editData.protein" placeholder="Protein"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>

                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="editData.fat" placeholder="Fat"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <Input v-model="editData.carbs" placeholder="Carbs"/>
                                </div>
                                <div class="col-2" style="display:flex; align-items:center; margin-left:-25px">
                                    <p style="font-weight:bold; font-size:17px;">%</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="space"></div>
                    <div class="space"></div>
                    <h2>Featured Image</h2>
                    <Upload v-show="isfeaturedImageNew" 
                     :format="['jpg', 'jpeg', 'png']" 
                     :headers="{
                            'x-csrf-token': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        }" :max-size="2048" :on-error="handleError"
                        :on-exceeded-size="handleMaxSize"
                        :on-format-error="handleFormatError" 
                        :on-success="handleSuccess" action="/app/upload" ref="editDataUploads" type="drag">
                        <div style="padding: 20px 0">
                            <Icon size="52" style="color: #3399ff" type="ios-cloud-upload"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>
                    <div class="imageview" v-show="viewMode">
                        <img height="200" width="200" :src="editData.featuredImage"/>
                    </div>
                    <div class="demo-upload-list" v-show="isEditingItem" v-if="editData.featuredImage">
                        <img :src="`${editData.featuredImage}`"/>

                        <div class="demo-upload-list-cover">
                            <Icon @click="deleteImage(false)" type="ios-trash-outline"></Icon>
                        </div>
                    </div>

                    <div slot="footer">
                        <Button @click="closeEditModal" type="error">Close</Button>
                        <Button v-show="isEditingItem" :disabled="isEditing" :loading="isEditing" @click="editMealPlan" type="primary">
                            {{ isEditing ? "Editing..." : "Edit Meal Plan" }}
                        </Button>
                    </div>
                </Modal>


            </div>
        </div>
    </div>
</template>

<script>


export default {
    data() {
        return {
            data: {
                title: "",
                description: "",
                calories: "",
                carbs: "",
                protein: "",
                fat: "",
                featuredImage: ""
            },
            addModal: false,
            isAdding: false,
            token: "",
            mealPlans: [],
            isEditing: false,
            editModal: false,
                editData: {
                title: "",
                description: "",
                calories: "",
                carbs: "",
                protein: "",
                fat: "",
                featuredImage: ""
            },
            isfeaturedImageNew: false,
            isEditingItem: false,
            viewMode:false
    }

    },
    methods: {
        async addMealPlan() {
            if (this.data.title.trim() == "")
                return this.error("Title is required");
            if (this.data.description.trim() == "")
                return this.error("Description is required");
            if (this.data.calories.trim() == "")
                return this.error("Calories cannot be empty");
            if (this.data.fat.trim() == "")
                return this.error("Fat is required");
            if (this.data.carbs.trim() == "")
                return this.error("Carbs cannot be empty");
            if (this.data.protein.trim() == "")
                return this.error("Protein is required");
            if (this.data.featuredImage.trim() == "")
                return this.error("Image is required");
            this.data.featuredImage = `${this.data.featuredImage}`;
            const res = await this.callApi(
                "post",
                "app/create_plan",
                this.data
            );
            if (res.status === 201) {
                this.success("Meal Plan added successfully");
                this.addModal = false;
                this.data.title = "";
                this.data.description = "";
                this.data.calories = "";
                this.data.carbs = "";
                this.data.protein = "";
                this.data.fat = "";
                this.data.featuredImage = "";
            } else {
                if (res.status === 422) {
                    if (res.data.errors.featuredImage) {
                        this.error(res.data.errors.featuredImage[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        async editMealPlan() {
            if (this.editData.title.trim() == "")
                return this.error("Title is required");
            if (this.editData.description.trim() == "")
                return this.error("Description is required");
            if (this.editData.calories.trim() == "")
                return this.error("Calories cannot be empty");
            if (this.editData.fat.trim() == "")
                return this.error("Fat is required");
            if (this.editData.carbs.trim() == "")
                return this.error("Carbs cannot be empty");
            if (this.editData.protein.trim() == "")
                return this.error("Protein is required");
            if (this.editData.featuredImage.trim() == "")
                return this.error("Image is required");
            this.data.featuredImage = `${this.data.featuredImage}`;
            const res = await this.callApi(
                "post",
                "app/edit_plan",
                this.editData
            );
            if (res.status === 200) {
                this.success("Meal Plan edited successfully");
                this.editModal = false;
                this.data.featuredImage = ""
                this.isEditingItem = false
                this.isEditing = false
                this.$refs.editDataUploads.clearFiles();


            } else {
                if (res.status === 422) {
                    if (res.data.errors.featuredImage) {
                        this.e(res.data.errors.featuredImage[0]);
                    }
                } else {
                    this.swr();
                }
            }
        },
        showEditModal(plan, index) {
			//to prevent real time editing 
			// let obj = {
			// 	id : plan.id,
            //     title: plan.title,
            //     description: plan.description,
            //     calories: plan.calories,
            //     carbs: plan.carbs,
            //     protein: plan.protein,
            //     fat: plan.fat,
            //     featuredImage: plan.featuredImage
			// }
			this.editData = plan
			this.editModal = true
            this.index = index
            this.isEditingItem = true
        },
        showViewModal(plan, index) {
            console.log('view mode activated')
            this.viewMode = true
            this.editModal = true
            this.editData = plan
            this.index = index
        },
        closeEditModal() {
            this.isEditingItem = false;
            this.editModal = false
            this.viewMode = false
        },
        handleSuccess(res, file) {
            res = `/uploads/${res}`
            if (this.isEditingItem) {
                return this.editData.featuredImage = res
            }
            this.data.featuredImage = res;
        },
        handleError(res, file) {
            this.$Notice.warning({
                title: "The file format is incorrect",
                desc: `${
                    file.errors.file.length
                        ? file.errors.file[0]
                        : "Something went wrong"
                }`
            });
        },
        handleFormatError(file) {
            this.$Notice.warning({
                title: "The file format is incorrect",
                desc:
                    "File format of " +file.name +" is incorrect, please select jpg or png."
            });
        },
        handleMaxSize(file) {
            this.$Notice.warning({
                title: "Exceeding file size limit",
                desc: "File  " + file.name + " is too large, no more than 2M."
            });
        },
        async deleteImage(isAdd=true) {
            let image
            if (!isAdd) { // for editing
                this.isfeaturedImageNew = true
                image = this.editData.featuredImage;
                this.editData.featuredImage = "";
                this.$refs.editDataUploads.clearFiles();
            }else{
            image = this.data.featuredImage;
            this.data.featuredImage = "";
            this.$refs.uploads.clearFiles();
            }
            const res = await this.callApi("post", "app/delete_image", {
                imageName: image
            });
            if (res.status != 200) {
                this.data.featuredImage = image;
                this.swr();
            }
            console.log(image);
        },
    },


        async created() {
        this.token = window.Laravel.csrfToken;
        const res = await this.callApi("get", "app/get_plans");
        if ((res.status = 201)) {
            this.mealPlans = res.data;
        } else {
            this.swr();
        }
    },
};
</script>

<style>
    .percentage {}
</style>