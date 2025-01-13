<template>
    <div class="form-group">
        <label class="control-label">
            Establecimiento
        </label>
        <el-select
            v-model="establishment_id"
            class="full"
            @change="changeEstablishment">
            <el-option v-for="option in records" :key="option.id" :value="option.id" :label="option.description"></el-option>
        </el-select>

        <small class="text-danger" v-if="errors_establishment_id" v-text="errors_establishment_id[0]"></small>
    </div>
</template>

<script>

    export default {
        props: {
            errors_establishment_id: Array,
        },
        data () {
            return {
                records: [],
                establishment_id: null,
            }
        },
        created()
        {
            this.initData()
        },
        methods: {
            cleanValue()
            {
                this.establishment_id = null
            },
            changeEstablishment()
            {
                this.$emit('changeEstablishment', this.establishment_id)
            },
            async initData()
            {
                await this.getData()
                            .then(response => {
                                this.records = response.data.data
                            })
            },
            async getData()
            {
                return await this.$http.get(`/establishments/records`)
            },
        }
    }
</script>