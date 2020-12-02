<template>
    <div>
        		<div class="content">
			<div class="container-fluid">
	
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Meal Plan Duration <Button icon="md-add" @click="addModal=true" >Add New </Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Title</th>
								<th>Description</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(duration, i) in mealplandurations" :key="i" v-if="mealplandurations.length" >
								<td>{{duration.id}}</td>
								<td class="_table_name">{{duration.title}}</td>
								<td >{{duration.description}}</td>
								<td>{{duration.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(duration, i)" >Edit</Button>
									<Button type="error" size="small"  @click="showDeletingModal(duration, i)">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->

						</table>
					</div>
				</div>

				<!--  adding modal -->
				<Modal
					title="Add Duration"
					:mask-closable="false"
					:closable="false"
                    v-model="addModal"
					>
					<Input v-model="data.title" placeholder="Title eg. 1-week(5 days)" />
                    <div class="space"></div>
					<Input  v-model="data.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="Description" />
					<div slot="footer">
						<Button type="error" @click="addModal=false">Close</Button>
						<Button type="primary" @click="add" :disabled="isAdding" :loading="isAdding ">{{isAdding ? 'Adding...' : 'Add Role'}}</Button>
					</div>
				</Modal>


				<!--  editing modal -->
				<Modal
					title="Edit Duration"
					:mask-closable="false"
					:closable="false"
                    v-model="editModal"
					>
					<Input v-model="editData.title" placeholder="Title eg. 1-week(5 days)" />
                    <div class="space"></div>
					<Input  v-model="editData.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="Description" />
					<div slot="footer">
						<Button type="error" @click="editModal=false">Close</Button>
						<Button type="primary" @click="edit" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing...' : 'Edit Duration'}}</Button>
					</div>
				</Modal>


				<deleteModal></deleteModal>


			</div>
		</div>
    </div>
</template>
<script>

import { mapGetters } from 'vuex';
import deleteModal from '../components/deleteModal.vue'
export default {
    data() {
        return {
            data:{
				title: '',
				description: ''
			},
            addModal: false,
            isAdding: false,
            mealplandurations: [],
                editData:{
                title: '',
                description: ''
            },
            editModal:false,
            isEditing: false,
            editingIndex: -1,
			showDeleteModal:false,
			deleteItem: {},
			isDeleting: false,
			deletingIndex: -1  
            
        }
    },
	components: {
        deleteModal
    },
    methods: {
		async add () {
            if (this.data.title.trim()=='') return this.error('Title is required');
            if (this.data.description.trim()=='') return this.error('Description is required');
            console.log(this.data)
            const res = await this.callApi('post', 'app/add_duration', this.data)
            console.log(res)
			if (res.status === 201) {
				this.mealplandurations.unshift(res.data)
				this.success('Duration added successfully')
				this.addModal = false
				this.data.title = ''
				this.data.description = ''
			}else {
				if (res.status===422) {
					if (res.data.errors.roleName) {
						this.e(res.data.errors.roleName[0])
					}
				}else{
				this.swr()
				}
			}
        },
        async edit(){
            if (this.editData.title.trim()=='') return this.error('Title is required');
            if (this.editData.description.trim()=='') return this.error('Description is required');			
            const res = await this.callApi('post', 'app/edit_duration', this.editData)
			if (res.status === 200) {
				this.mealplandurations[this.editingIndex].title = this.editData.title
				this.mealplandurations[this.editingIndex].description = this.editData.description
				this.success('Duration edited successfully')
				this.editModal = false
			}else {
				if (res.status===422) {
					if (res.data.errors.roleName) {
						this.e(res.data.errors.roleName[0])
					}
				}else{
				this.swr()
				}
			}

        },
        showEditModal(plan, index) {
			//to prevent real time editing 
			let obj = {
				id : plan.id,
				title : plan.title,
				description : plan.description
			}
			this.editData = obj
			this.editModal = true
            this.editingIndex = index
        },
		showDeletingModal(duration, i) {
            console.log('the index is ', i)
                this.deletingIndex = i
                const deleteModalObj = {
                showDeleteModal: true,
                deleteUrl: 'app/delete_duration',
                data: duration,
                deletingIndex: i,
                isDeleted: false,
                msg: 'Are you sure you want to delete this Duration?',
                successMsg: 'Duration deleted successfully'

            }
            this.$store.commit('setDeletingModalObj', deleteModalObj)
            console.log('delete method called ')
            }
    },
    async created() {
            const res = await this.callApi('get', 'app/get_durations')
            console.log(res)
            if (res.status === 200) {
                this.mealplandurations = res.data
            }
    },

        	computed: {
		...mapGetters([
			'getDeleteModalObj'
		])
	},
	watch: {
		getDeleteModalObj(obj) {
			console.log(obj)
			if (obj.isDeleted) {
				this.mealplandurations.splice(this.deletingIndex, 1)

			}
		}
	}


    
}
</script>