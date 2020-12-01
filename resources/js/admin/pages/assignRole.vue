<template>
    <div>
        		<div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Role Management 
                        <Select v-model="data.id"  placeholder="Select Admin Type"
                        style="width:300px;" @on-change="changeAdmin"
                        >
                    <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                        </Select>
						<span style="float:right">Check All <Checkbox v-model="checkAllBoxes" @on-change="checkAll"></Checkbox> </span>
                    </p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Resource Name</th>
								<th>Read</th>
								<th>Write</th>
								<th>Update</th>
								<th>Delete</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(r, i) in resources" :key="i" >
								<td>{{r.resourceName}}</td>
								<td><Checkbox v-model="r.read"></Checkbox></td>
								<td><Checkbox v-model="r.write"></Checkbox></td>
								<td><Checkbox v-model="r.update"></Checkbox></td>
								<td><Checkbox v-model="r.delete"></Checkbox></td>
								
							</tr>
								<!-- ITEMS -->
						</table>
							<div class="center_button pt-3">
								<Button @click="assignRoles" type="primary" :loading="isSending" :disabled="isSending">Assign</Button>
							</div>
					</div>
				</div>

			</div>
		</div>
    </div>
</template>

<script>


export default {
	data() {
		return {
			data:{
				roleName: '',
				id: null,
			},
			isSending: false,
			isAllChecked: false,
			roles: [],
			checkAllBoxes: false,
			resources: [
				{resourceName: 'Home', read: true, write: false, update: false, delete: false, name: '/'},
				{resourceName: 'Meal Plans', read: true, write: false, update: false, delete: false, name: 'mealplans'},
				{resourceName: 'Meal Plan Durations', read: true, write: false, update: false, delete: false, name: 'mealplandurations'},
				{resourceName: 'Duration Packages', read: true, write: false, update: false, delete: false, name: 'durationpackages'},
				{resourceName: 'Adminusers', read: true, write: false, update: false, delete: false, name: 'adminusers'},
				{resourceName: 'Roles', read: true, write: false, update: false, delete: false, name: 'roles'},
				{resourceName: 'AssignRole', read: true, write: false, update: false, delete: false, name: 'assignRole'},
				],
			
			defaultResourcesPermission: [
				{resourceName: 'Home', read: false, write: false, update: false, delete: false, name: '/'}, 				
				{resourceName: 'Meal Plans', read: false, write: false, update: false, delete: false, name: 'mealplans'},
				{resourceName: 'Meal Plan Durations', read: false, write: false, update: false, delete: false, name: 'mealplandurations'},
				{resourceName: 'Duration Packages', read: false, write: false, update: false, delete: false, name: 'durationpackages'},
				{resourceName: 'Adminusers', read: false, write: false, update: false, delete: false, name: 'adminusers'},
				{resourceName: 'Roles', read: false, write: false, update: false, delete: false, name: 'roles'},
				{resourceName: 'AssignRole', read: false, write: false, update: false, delete: false, name: 'assignRole'},
				],
			checkAllPermission: [
				{resourceName: 'Home', read: true, write: true, update: true, delete: true, name: '/'}, 				
				{resourceName: 'Meal Plans', read: true, write: true, update: true, delete: true, name: 'mealplans'},
				{resourceName: 'Meal Plan Durations', read: true, write: true, update: true, delete: true, name: 'mealplandurations'},
				{resourceName: 'Duration Packages', read: true, write: true, update: true, delete: true, name: 'durationpackages'},
				{resourceName: 'Adminusers', read: true, write: true, update: true, delete: true, name: 'adminusers'},
				{resourceName: 'Roles', read: true, write: true, update: true, delete: true, name: 'roles'},
				{resourceName: 'AssignRole', read: true, write: true, update: true, delete: true, name: 'assignRole'},
				],
		}
	},
	components: {
        
    },
	methods: {
		async  assignRoles() {
			let data = JSON.stringify(this.resources)
			const res = await this.callApi('post','app/assign_roles', {'permission': data, id: this.data.id})
			if (res.status == 200) {
				this.success('Role has been assigned successfully')
			} else {
				this.swr()
			}
		},
		changeAdmin() {
			let index = this.roles.findIndex(role => role.id == this.data.id)
			let permission = this.roles[index].permission
			//console.log('index permision is', this.roles[index].permission)
			console.log('permission is ', permission)
			if (!permission) {
				this.resources = this.defaultResourcesPermission
			} else {
			this.resources = JSON.parse(permission)
			}
					},
		checkAll() {
			let comp = _.isEqual(this.checkAllPermission, this.resources)
			//console.log(comp)
			if (comp) {
			this.resources = this.defaultResourcesPermission

			} else {
			this.resources = this.checkAllPermission
			}
			//console.log('permission is', this.resources)
		}

	},
	async created() {
		//console.log(this.$route)
		const res = await this.callApi('get', 'app/get_roles')
		if (res.status == 200) {
			console.log('roles: ', res.data)
			this.roles = res.data
			if (res.data.length) {
			this.data.id = res.data[0].id;
			console.log('the id is ',res.data[0].id)
			console.log( 'roles permission', res.data[0].permission)
			if (res.data[0].permission) {
				//let index = this.roles.findIndex(role => role.id == this.data.id)
				console.log(typeof(res.data[0].permission))
				this.resources = JSON.parse(res.data[0].permission)
				//console.log('the parsed json', JSON.parse(res.data[0].permission))
				//this.resources = this.defaultResourcesPermission
			}			
			}
		} else {
			this.swr()
		}
	},
	updated() {
		let compare = _.isEqual(this.checkAllPermission, this.resources)
					//console.log(comp)
					if (compare) {
						this.checkAllBoxes = true
					} else {
						this.checkAllBoxes = false
					}
	}
}
</script>