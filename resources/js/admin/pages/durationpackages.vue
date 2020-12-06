<template>
    <div>
        		<div class="content">
			<div class="container-fluid">
                <div class="space"></div>
                    <Row :gutter="16">
                        <Col :xs="24" :sm="24" :md="12" :lg="6">
                            <h2 style="margin-bottom:15px;">Duration Packages</h2>
                        <div v-if="mealplandurations.length">
                            <Collapse  v-for="(duration, i) in mealplandurations" :key="i" class="package_panel" style=" margin: 10px;">
                                <Panel name="1">
                                    {{duration.title}}
                                    <span style="float:right; margin-right:5px;">
                                                            <Tooltip content="Add a new Meal Package"> 
                                                            <Button :size="buttonSize" type="default" icon="md-add"  @click="showAddModal(duration, i)" shape="circle" >
                                                            </Button>
                                                            </Tooltip>
                                    </span>
                                    <p slot="content">{{duration.description}}</p>
                                </Panel>
                            </Collapse>
                            </div>
                        </Col>
                        <Col :xs="24" :sm="24" :md="12" :lg="18">
                            <h2 class="text-center mb-3">Meal Packages</h2>
                            
                            <Row :gutter="16">
                        <div v-if="mealpackages.length">
                            <Col v-for="(mealpackage, i) in mealpackages" :key="i" :xs="24" :sm="24" :md="12" :lg="8">
                            <div class="" >
                            <div class="clash-card barbarian" >
                                <div v-bind:style="{ backgroundImage: 'url(' + mealpackage.featuredImage + ')' }" class="clash-card__image clash-card__image--barbarian">
                                </div>
                                <div class="clash-card__level clash-card__level--barbarian">{{mealpackage.title}}</div>
                                <div class="clash-card__unit-name"></div>
                                <div v-html="mealpackage.recipe" class="clash-card__unit-description">
                                    
                                </div>
                        
                                <div class="clash-card__unit-stats clash-card__unit-stats--barbarian clearfix">
                                <div class="one-third">
                                    <div class="stat">{{mealpackage.price}}</div>
                                </div>
                                
                                <div class="one-third">
                                    <div class="stat"><Tooltip content="Edit"> <Icon @click="showEditPackage(mealpackage, i)" type="md-create" /> </Tooltip> </div>
                                </div>
                        
                                <div class="one-third no-border">
                                    <div class="stat"><Tooltip content="Delete"> <Icon type="ios-trash" /></Tooltip></div>
                                </div>
                        
                                </div>
                        
                            </div> <!-- end clash-card barbarian-->
                            </div> <!-- end wrapper -->
                
                            </Col>
                        </div>
                            </Row>



                        </Col>
                    </Row>
			</div>
		</div>


				<!--  adding modal -->
				<Modal
					title="Add Package"
					:mask-closable="false"
					:closable="false"
                    v-model="addModal"
                    style="width:400px"
					>
					<Input v-model="data.title" placeholder="Title"/>
                    <div class="space"></div>
                    <Row>
                        <Col span="24">
                        <vue-editor v-model="data.recipe" :editorToolbar="customToolbar"></vue-editor>   
                        </Col>
                    </Row>
                    <div class="space"></div>
                    <Row :gutter="16">
                        <Col span="12">
                        <p class="title">Meal Plan</p>
						<Select  filterable multiple placeholder="Select Meal Plan" v-model="data.mealplans_id">
							<Option v-for="(m, i) in mealplans" :value="m.id" :key="i">{{m.title}}</Option>
						</Select>
                        </Col>
                        <Col span="12">
                        <p class="title">Price</p>
					        <Input v-model="data.price" placeholder=""/>
                        </Col>
                    </Row>
                            <div class="space"></div>
                            <p class="title">Featured Image</p>
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
						<Button type="error" @click="addModal=false">Close</Button>
						<Button type="primary" @click="add" :disabled="isAdding" :loading="isAdding ">{{isAdding ? 'Adding...' : 'Add Package'}}</Button>
					</div>
				</Modal>

				<!--  editing package modal -->
				<Modal
					title="Edit Package"
					:mask-closable="false"
					:closable="false"
                    v-model="editPackageModal"
                    style="width:400px"
					>
					<Input v-model="editPackageData.title" placeholder="Title"/>
                    <div class="space"></div>
                    <Row>
                        <Col span="24">
                        <vue-editor v-model="editPackageData.recipe" :editorToolbar="customToolbar"></vue-editor>   
                        </Col>
                    </Row>
                    <div class="space"></div>
                    <Row :gutter="16">
                        <Col span="12">
                        <p class="title">Meal Plan</p>
						<Select  filterable  placeholder="Select Meal Plan" v-model="editPackageData.mealplan_id">
							<Option v-for="(m, i) in mealplans" :value="m.id" :key="i">{{m.title}}</Option>
						</Select>
                        </Col>
                        <Col span="12">
                        <p class="title">Price</p>
					        <Input type="number" v-model="editPackageData.price" placeholder=""/>
                        </Col>
                    </Row>
                            <div class="space"></div>
                            <p class="title">Featured Image</p>
                            <Upload v-show="isfeaturedImageNew"
                            :format="['jpg', 'jpeg', 'png']" :headers="{
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

                            <div class="demo-upload-list" v-show="isEditingItem" v-if="editPackageData.featuredImage">
                                <img :src="`${editPackageData.featuredImage}`"/>

                                <div class="demo-upload-list-cover">
                                    <Icon @click="deleteImage(false)" type="ios-trash-outline"></Icon>
                                </div>
                            </div>                      


					<div slot="footer">
						<Button type="error" @click="editPackageModal=false">Close</Button>
						<Button type="primary" @click="editPackage" :disabled="isEditing" :loading="isEditing ">{{isEditing ? 'Editing...' : 'Edit Package'}}</Button>
					</div>
				</Modal>




    </div>
</template>
<script>
import { VueEditor } from "vue2-editor";

export default {
        data() {
            return {
                buttonSize: 'small',
                durations:[],
                mealplandurations: [],
                mealplans:[],
                addModal: false,
                isAdding:false,
                data: {
                    title: '',
                    recipe: '',
                    price: '',
                    featuredImage: '',
                    mealplanduration_id: null,
                    mealplans_id: [],
                },
                customToolbar: [["bold", "italic", "underline"], [{ list: "ordered" }, { list: "bullet" }], ["image", "code-block"]],
                token: false,
                mealpackages: [],
                isEditing: false,
                editPackageModal: false,
                editPackageData: {
                    title: "",
                    recipe: "",
                    price: null,
                    featuredImage: "",
                    mealplanduration_id: null,
                    mealplan_id: null
                },
                editingIndex: -1,
                isfeaturedImageNew: false,
                isEditingItem: false,


            }
        },
    components: {
    VueEditor
    },
    methods: {
        showAddModal(duration, index) {
            this.data.mealplanduration_id = duration.id
			this.addModal = true
        },
        async add() {
            if (this.data.title.trim() == "")
                return this.error("Title is required");
            if (this.data.recipe.trim() == "")
                return this.error("Recipe is required");
            if (!this.data.mealplans_id.length)
                return this.error("Meal plan  is required");
            if (this.data.price.trim() == "")
                return this.error("Price is required");
            if (this.data.featuredImage.trim() == "")
                return this.error("Image is required");
            this.data.featuredImage = `${this.data.featuredImage}`;
            
            const res = await this.callApi(
                "post",
                "app/create_package",
                this.data
            );
            if (res.status === 200) {
                this.success("Package added successfully");
                this.addModal = false;
                this.data.title = "";
                this.data.recipe = "";
                this.data.price = "";
                this.data.featuredImage = "";
                this.data.mealplans_id = "";
                this.mealplanduration_id = ""
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
        handleSuccess(res, file) {
            res = `/uploads/${res}`
            if (this.isEditingItem) {
                return this.editPackageData.featuredImage = res
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
                image = this.editPackageData.featuredImage;
                console.log(image)
                //check if there are multiple meal packages with the same image
            const countImage = await this.callApi("post", "app/count_images", {
                imageName: image
            });
            console.log(countImage.data)
            if(countImage.data == 1) {
                console.log('its just one')
            const res = await this.callApi("post", "app/delete_image", {
                imageName: image
            }); 
            if (res.status != 200) {
            this.data.featuredImage = image;
            this.swr();
                }
            }
            console.log(image);             
                this.editPackageData.featuredImage = "";
                this.$refs.editDataUploads.clearFiles();
            
            }else {
            image = this.data.featuredImage;
            this.data.featuredImage = "";
            this.$refs.uploads.clearFiles();
            const res = await this.callApi("post", "app/delete_image", {
                imageName: image
            });
                if (res.status != 200) {
            this.data.featuredImage = image;
            this.swr();
            }
            console.log(image);
            }

        },
        showEditPackage(mealpackage, i) {
			//to prevent real time editing 
			let obj = {
				id : mealpackage.id,
				title : mealpackage.title,
                recipe: mealpackage.recipe,
                price: mealpackage.price,
                featuredImage: mealpackage.featuredImage,
                mealplanduration_id: mealpackage.mealplanduration_id,
                mealplan_id: mealpackage.mealplan_id
            }
			this.editPackageData = obj
			this.editPackageModal = true
            this.isEditingItem = true
			this.editingIndex = i
        },
        async editPackage() {
            if (this.editPackageData.title.trim() == "")
                return this.error("Title is required");
            if (this.editPackageData.recipe.trim() == "")
                return this.error("Recipe is required");
            if (this.editPackageData.price == "")
                return this.error("price cannot be empty");
            if (this.editPackageData.mealplan_id == null)
                return this.error("Meal Plan is required");
            if (this.editPackageData.featuredImage.trim() == "")
                return this.error("Image is required");
            this.data.featuredImage = `${this.data.featuredImage}`;
            const res = await this.callApi(
                "post",
                "app/edit_package",
                this.editPackageData
            );
            if (res.status === 200) {
				this.mealpackages[this.editingIndex] = this.editPackageData
                this.success("Meal Package edited successfully");
                this.editPackageModal = false;
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

    },




			async created() {
        this.token = window.Laravel.csrfToken;
			const [durations, plans, packages ] = await Promise.all([
				this.callApi('get', 'app/get_durations'),
				this.callApi('get', 'app/get_plans'),				
				this.callApi('get', 'app/get_packages'),				
			]) 
			if (durations.status==200) {
				this.mealplandurations = durations.data
                this.mealplans = plans.data
                this.mealpackages = packages.data
			}else {
				this.swr()
			}
		}



}
</script>

<style scoped>
.btn-sm{
    padding: 8px;
}
.duration_card{
    padding: 0px !important;
}
.ivu-modal-content{
    width: 800px; 
}
</style>