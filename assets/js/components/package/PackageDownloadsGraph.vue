
<script>

    import { Line, mixins } from 'vue-chartjs'
    const { reactiveProp } = mixins;

    export default {
        name: "package-downloads-graph",
        extends: Line,
        mixins: [reactiveProp],
        // this.chartData is created in the mixin.
        mounted () {
            Chart.defaults.global.defaultFontColor = '#fff';
            this.renderChart(this.chartdata, this.options)
        },
        data () {
            return {
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        labels: {
                            fontColor: '#ffffff'
                        }
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'month'
                            }
                        }]
                    }
                }
            }
        }
    }
</script>
