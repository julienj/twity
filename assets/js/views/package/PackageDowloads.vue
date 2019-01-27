<template>

    <div class="card bg-grey list-item" >
        <div class="card-header">
            Package installs
        </div>
        <div class="card-body" >
            <package-downloads-graph :height="500"  :chart-data="datacollection"></package-downloads-graph>
        </div>
    </div>

</template>

<script>

    import PackageDownloadsGraph from '../../components/package/PackageDownloadsGraph'
    import ProviderApi from '../../api/provider'

    export default {
        name: "package-dowloads",
        props: ['currentPackage'],
        components: {
            PackageDownloadsGraph
        },
        data () {
            return {
                datacollection: null
            }
        },
        mounted () {
            this.fillData()
        },
        methods: {
            fillData () {

                ProviderApi.getStats(this.currentPackage.data.name, this.currentPackage.data.version_normalized)
                    .then( (response) => {

                        let data = response.data;
                        let versionLineData = [];
                        let allLineData = [];
                        let labels = [];

                        for (let label in data) {
                            labels.push(label);
                            if (typeof data[label]['all'] !== 'undefined') {
                                allLineData.push( data[label]['all']);
                            } else {
                                allLineData.push(0);
                            }
                            if (typeof data[label]['version'] !== 'undefined') {
                                versionLineData.push( data[label]['version']);
                            } else {
                                versionLineData.push(0);
                            }
                        }

                        this.datacollection = {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'All version',
                                    borderColor: '#EE7407',
                                    data: allLineData,
                                    fill: false
                                }, {
                                    label: this.currentPackage.data.version,
                                    borderColor: '#4285F4',
                                    data: versionLineData,
                                    fill: false
                                }
                            ]
                        }

                    })
            }
        },
        watch: {
            'currentPackage' : function () {
                this.fillData();
            }
        }
    }
</script>
