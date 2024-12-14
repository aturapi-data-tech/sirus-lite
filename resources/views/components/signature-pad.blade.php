<div x-data="signaturePad(@entangle($attributes->wire('model')))">

    <div>
        <canvas x-ref="signature_canvas" class="border rounded-lg shadow">
        </canvas>
    </div>

    <div class="flex justify-end mt-4">

        <x-red-button @click="signaturePadInstance.clear()">
            {{ __('Clear Signature') }}
        </x-red-button>
    </div>

    @push('scripts')
        <script src="assets/js/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('signaturePad', (value) => ({
                    signaturePadInstance: null,
                    value: value,
                    init() {

                        this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas, {
                                minWidth: 2,
                                maxWidth: 2,
                                penColor: "rgb(11, 73, 182)"
                            }

                        );
                        this.signaturePadInstance.addEventListener("endStroke", () => {
                            // this.value = this.signaturePadInstance.toDataURL('image/png');signaturePad.toSVG()
                            // https://github.com/aturapi-data-tech/signature_pad
                            // https://gist.github.com/jonneroelofs/a4a372fe4b55c5f9c0679d432f2c0231
                            this.value = this.signaturePadInstance.toSVG();

                            // console.log(this.signaturePadInstance)
                        });
                    },
                }))
            })
        </script>
    @endpush


</div>
