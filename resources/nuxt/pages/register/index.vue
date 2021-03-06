<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center>
			<v-flex xs12 sm8 md5>
				<v-card class="card-radius">
					<v-toolbar class="elevation-0" dense color="transparent">
						<v-layout row wrap justify-center align-center>
							<v-toolbar-title>
								{{title}}
							</v-toolbar-title>		
						</v-layout>						
					</v-toolbar>
					<v-progress-linear indeterminate background-color="grey lighten-3"	color="white" class="py-0 my-0" v-if="loading"></v-progress-linear>
					<v-card-text class="white">
						
						<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name" :icon="alert.type == 'error' ? 'warning' : 'priority_high' ">
							{{alert.message}}
						</v-alert>

						<v-form>		
							<v-layout row wrap>			
								<v-flex xs12 md12>		
									<v-text-field color="red accent-3" prepend-icon="person" v-model="editedItem.name" label="Họ và tên" type="text" 
									v-validate="'required|max:100'"
									data-vv-name="name"
									:error-messages="errors.collect('name')"
									data-vv-delay="300"></v-text-field>
								</v-flex>
								<v-flex xs12 md12>
									<v-text-field color="red accent-3"  prepend-icon="email" v-model.trim="editedItem.email" label="Địa chỉ email" type="text"
									v-validate="'required|email'"
									data-vv-name="email"
									:error-messages="errors.collect('email')"
									data-vv-delay="300"
									hint="Địa chỉ email là bắt buộc và chính xác"
									persistent-hint></v-text-field>
								</v-flex>
								<v-flex xs12 md6>
									<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="editedItem.password" label="Mật khẩu" id="password" type="password"
									v-validate="'required|min:8|max:36'"
									data-vv-name="password"
									:error-messages="errors.collect('password')"
									data-vv-delay="300"
									:type="showPassword ? 'text' : 'password'"
									hint="Sử dụng 8 ký tự trở lên và kết hợp chữ cái, chữ số và biểu tượng"
									persistent-hint></v-text-field>
								</v-flex>
								<v-flex xs12 md6>
									<v-text-field color="red accent-3"  prepend-icon="lock" v-model.trim="confirm" name="confirm" label="Xác nhận mật khẩu" id="confirm" type="password"
									v-validate="{required: true, is:editedItem.password}"
									data-vv-name="confirm"
									:error-messages="errors.collect('confirm')"
									data-vv-delay="300"
									:type="showPassword ? 'text' : 'password'"
									:append-icon="showPassword ? 'visibility' : 'visibility_off'"
									@click:append="showPassword = !showPassword"
									></v-text-field>
								</v-flex>
								<v-flex xs12 md6>

									<v-menu ref="menu" lazy :close-on-content-click="false" v-model="menu" transition="scale-transition" offset-y lazy full-width :nudge-right="40" min-width="290px" >
										<v-text-field color="red accent-3" slot="activator" label="Ngày sinh" v-model="dateFormatted" prepend-icon="event" v-validate="'required'" :error-messages="errors.collect('birthday')" data-vv-name="birthday" readonly></v-text-field>

										<v-date-picker 
										ref="picker"
										color="red accent-3" 
										locale="vi-vn"  
										v-model="editedItem.birthday" 
										min="1950-01-01" 
										:max="new Date().toISOString().substr(0, 10)" 
										@change="$refs.menu.save(editedItem.birthday)" ></v-date-picker>
									</v-menu>

								</v-flex>
								<v-flex xs12 md6>
									<v-radio-group 
									v-model="editedItem.gender" 
									row
									v-validate="{required:true}"
									:error-messages="errors.collect('gender')"
									data-vv-name="gender">
									<v-radio color="red accent-3" label="Nam" :value="true" 
									></v-radio>
									<v-radio color="red accent-3" label="Nữ" :value="false"
									></v-radio></v-radio-group>
								</v-flex>
								<v-flex xs12 md12>
									<v-text-field color="red accent-3"  prepend-icon="phone" v-model.trim="editedItem.phone" name="confirm" label="Số điện thoại"
									v-validate="'required|numeric|min:10|max:10'"
									mask="(####) ### - ###"
									data-vv-name="phone"
									:error-messages="errors.collect('phone')"
									data-vv-delay="300"
									hint="Số điện thoại chỉ đăng ký được một tài khoản"
									persistent-hint></v-text-field>
								</v-flex>
							</v-layout>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-btn color="red accent-3" dark block :loading="loading" @click.stop.prevent="register" round small>Đăng ký</v-btn>
					</v-card-actions>
					<v-card-actions>
						<v-btn color="blue" flat :to="{path: '/login'}" small>
							<v-icon>chevron_left</v-icon>
							Quay lại
						</v-btn>
					</v-card-actions>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import axios from 'axios'
	import { registerURL } from '@/config'
	import { mapState } from 'vuex'
	import vietnam from 'vee-validate/dist/locale/vi';
	export default {
		layout: 'credential',
		head() {
			return {
				title: 'Tạo tài khoản'
			}
		},
		asyncData() {
			return {
				title: 'Tạo tài khoản',
				editedItem: {
					name: '',
					email: '',
					password: '',
					gender: false,
					birthday: null,
					phone: ''
				},
				dateFormatted: null,
				confirm: '',
				menu:false,
				loading: false,
				locale: 'vi',
				test:false,
				showPassword: false
			}
		},
		watch: {
			menu(val) {
				val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
			},
			'editedItem.birthday': function(val) {
				this.dateFormatted = this.formatDate(this.editedItem.birthday)
				if(val) {
					this.errors.clear()
				}
			}
		},
		methods: {	
			register: function() {
				var vm = this

				let data = Object.assign({}, vm.editedItem)			

				vm.$validator.validateAll().then(async function(result) {
					if(result) {

						if(!vm.loading) {
							vm.loading = !vm.loading

							setTimeout(() => {
								axios.post('/api/register', data).then(response => {

									if(response.status === 201) {

										vm.$store.dispatch('alert', {
											name: 'login',
											index:0,
											type: 'success',
											message: 'Tài khoản đã được tạo thành công',
											close: true,
										})

										vm.$router.push({name: 'login'})

									}
								}).catch(error => {
									if(error.response.status == 422) {
										var mes = ''

										Object.values(error.response.data.errors).map((item) => {
											mes += item[0] + ' ';
										})

										vm.$store.dispatch('alert', {
											name: vm.$route.name,
											index:0,
											type: 'error',
											message: mes,
											close: true
										})
									}
								}).finally(() => {			
									vm.loading = !vm.loading
								})

							}, 100)
						}
					}
				})
			},
			formatDate (date) {
				if (!date) return null
					const [year, month, day] = date.split('-')
				return `${day}/${month}/${year}`
			},
		},
		computed: {
			...mapState({
				alert: state => state.alertStore.alert
			}),
			show() {
				if(this.$route.name === this.alert.name && this.alert.show) {
					return true
				}
				return false
			}
		},
		created() {
			this.$validator.localize(this.locale, {
				messages:vietnam.messages,
				attributes: {
					name: 'Họ tên',
					email: 'Email',
					password: 'Mật khẩu',
					confirm: 'Xác nhận mật khẩu',
					birthday: 'Ngày sinh',
					gender: 'Giới tính',
					phone: 'Số điện thoại'
				}
			})
			this.$validator.localize(this.locale)
		}
	}	
</script>

