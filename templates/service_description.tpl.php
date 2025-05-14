<?php
declare(strict_types=1);
?>

<?php function drawServiceDescription(Service $service): void { ?>
    <section class="service-description">
        <div class="photo-selector">
            <div class="main-photo">
                <img id="selected-photo" src="https://picsum.photos/300/200?b" alt="Selected Photo">
            </div>
            <div class="thumbnail-photos">
                
                    <img class="thumbnail" src="https://picsum.photos/400/200?b>" alt="Thumbnail" onclick="selectPhoto(this)">
            </div>
        </div>

        <script>
            function selectPhoto(thumbnail) {
                const mainPhoto = document.getElementById('selected-photo');
                temp= mainPhoto.src;
                mainPhoto.src = thumbnail.src;
                thumbnail.src = temp;
            }
        </script>

        <style>
            .photo-selector {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .main-photo img {
                width: 400px;
                height: auto;
                margin-bottom: 10px;
            }

            .thumbnail-photos {
                display: flex;
                gap: 10px;
            }

            .thumbnail-photos img {
                width: 100px;
                height: auto;
                cursor: pointer;
                transition: transform 0.2s;
            }

            .thumbnail-photos img:hover {
                transform: scale(1.1);
            }
        </style>
         
    </section>
<?php } ?>
