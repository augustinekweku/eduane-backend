<template>
    <div>
        		<div class="content">
			<div class="container-fluid">
                <h1>~Duration Packages</h1>
                <div class="space"></div>
                    <Row :gutter="16">
                        <Col v-for="(duration, i) in mealplandurations" :key="i"  :xs="24" :sm="24" :md="12" :lg="8">
                            <Collapse v-if="mealplandurations.length" class="package_panel" style=" margin: 10px;">
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
                token: false


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
			const [d, p] = await Promise.all([
				this.callApi('get', 'app/get_durations'),
				this.callApi('get', 'app/get_plans'),				
			]) 
			if (d.status==200) {
				this.mealplandurations = d.data
				this.mealplans = p.data
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