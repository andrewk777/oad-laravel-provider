<template>
<div>

	<div v-show="canUpload" class="file-drop" v-cloak @drop.prevent="droppedFiles" @dragover.prevent @click="$refs[data.id].click()">
		<input type="file" :id="data.id" :ref="data.id" @change="selectedFiles($event)" style="display: none" multiple />
		Click here or drop files
	</div>

	<SmoothReflow>
		<b-progress class="mt-2" v-show="showProgress" :value="progressPercentage" max="100" variant="success" show-progress animated></b-progress>
	</SmoothReflow>

	<SmoothReflow>
		<ul class="files-list" v-show="uploadedFiles.length">
			<li v-for="(file,index) in uploadedFiles" v-bind:key="index">
				<b-button v-if="data.can_delete" @click="removeFileRequest(file)" variant="outline-danger" size="xs"><i class="fal fa-times"></i></b-button>
				<a :href="filePath(file.hash)" target="_blank">{{ file.name }}</a>
			</li>
		</ul>
	</SmoothReflow>

</div>
</template>

<script>

import field from '@/mixins/field';
import { CONF } from '@/config';

export default {
	mixins: [field],
	data() {
		return {
			addedFiles: [],
			uploadedFiles: [],
			showProgress: false,
			progressPercentage: 0
		}
	},
	computed: {
		canUpload() {
			return !(this.data.max_files && this.uploadedFiles.length >= this.data.max_files);
		}
	},
	watch: {
		value(val) {
			if (!Array.isArray(val)) // means it was not just an upload, but a value forced from the parent
				this.uploadedFiles = this.value;
		}
	},
	methods: {
		filePath(hash) {
			return '/view_file/' + hash;
		},
		selectedFiles(e) {
			this.initUpload(e.target.files);
		},
		droppedFiles(e) {
			this.initUpload(e.dataTransfer.files);
		},
		initUpload(droppedFiles) {
	
			if(!droppedFiles) return;

			([...droppedFiles]).forEach(f => {
				this.addedFiles.push(f);
			});

			if (this.data.max_files && this.addedFiles.length + this.uploadedFiles.length > this.data.max_files) {
				this.addedFiles = [];
				this.$utils.c_notify('Max files allowed ' + this.data.max_files, 'error');
				return false;
			} else if (this.data.allowed_ext.length > 0) {

				for (var i = 0; i < this.addedFiles.length; i++) {
					var ext = this.addedFiles[i].name.split(/[\. ]+/).pop().toLowerCase();
					if (!this.data.allowed_ext.includes(ext)) {
						this.$utils.c_notify('File '+this.addedFiles[i].name+' was not added.<br>File type '+ext+' is not allowed', 'error');
						this.addedFiles.splice(i,1)
					}
				}
				if (this.addedFiles.length == 0) return false;
			}

			this.showProgress = true;
			setTimeout(() => this.uploadFiles(), 200);
		},
		uploadFiles() {
			var self = this;

			let formData = new FormData();
			this.addedFiles.forEach((f,x) => {
			   formData.append('file'+(x+1), f);
			 });

			this.$http.post(CONF.API_URL + '/file-upload',
				formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					},
					onUploadProgress: function(progressEvent) {
						this.progressPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
					}.bind(this)

				}).then(function(res) {
					setTimeout(function() {
						for (var i = 0; i < res.data.length; i++) {
							self.uploadedFiles.unshift(res.data[i]);
						}
						self.emitData(self.uploadedFiles)
					}
					, 300)
				})
				.catch(function() {
				})
				.then(function(res) {	
					setTimeout(function() {
						self.addedFiles = [];
						self.showProgress = false
						self.progressPercentage = 0;
					}
					, 300)
				});
			},
			removeFileRequest(file){
				if (this.data.confirm_on_delete) {
					this.$utils.swalConfigm.fire({
						title: "Delete '" + file.name + "' file?",
						text: '',
						showCancelButton: true,
						confirmButtonText: 'Confirm'
					}).then((result) => {
						if (result.value) {
							this.delete(file);
						}
					})
				} else {
					this.delete(file);
				}
			},
			delete(file) {
				this.uploadedFiles = this.uploadedFiles.filter(f => {
					return f != file;
				});
				this.$emit('file-removed', { field: this.data.name, hash: file.hash, name: file.name })
				this.emitData(this.uploadedFiles)
			},
			emitData() {
				var data = [];

				for (var i = 0; i < this.uploadedFiles.length; i++) {
					data.push(this.uploadedFiles[i].hash);
				}
				this.emitChild(data)
			}
		},
		mounted() {
			this.uploadedFiles = this.value ? this.value : [];
			this.emitData(this.uploadedFiles);
		}
	}
</script>

<style scoped>
.files-list {
	padding: 10px;
	list-style: none;
}
.btn-outline-danger {
	padding: 1px 6px;
	border: none;
}
</style>
