<script>
    import { Line, mixins } from 'vue-chartjs'
    const { reactiveProp } = mixins

    export default {
        extends: Line,
        mixins: [reactiveProp],
        mounted () {
            Chart.defaults.global.defaultFontColor = '#fff';
            this.renderChart(this.chartData, this.options)
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

