					<div class="top-featured media-player-cont">
						<div class="media-player-holder"></div>
						<div class="featured-wrap">
							<div class="current-featured media-player">
								<div class="featured-player">
									<div class="player-overlay">
										<div class="player-controls">
											<span class="backward prev" title="Previous"><i class="fa fa-backward"></i></span>
											<span class="play-pause pause jp-pause" style="display: none;" title="Pause"><i class="fa fa-pause"></i></span>
											<span class="play-pause play jp-play" title="Play"><i class="fa fa-play"></i></span>
											<span class="forward next" title="Next"><i class="fa fa-forward"></i></span>
											<div class="player-pop">
												<span title="Pop out media player" class="pop-out"><i class="fa fa-caret-square-o-up"></i></span>
											</div>											
										</div>
									</div><!-- player-overlay -->
									<div class="featured-image">
										<?php
										if(isset($scPosts[0]) AND trim($scPosts[0]['coverImage']) != ''){
											echo '<img src="'.$scPosts[0]['coverImage'].'" alt="" />';
										}
										?>
									</div><!-- featured-image -->
								</div><!-- featured-player -->							
								<div class="featured-content">
									<h2>On Today's Show</h2>
									<div class="featured-title track-title">
										<p class="track">
											<?= $scPosts[0]['title'] ?>
										</p>
									</div><!-- featured-title -->
									<div class="featured-date">
										<a href="#" class="pop-out-player" title="Pop out audio player"></a>
										<span><?= strtoupper(date('jS F Y', strtotime($scPosts[0]['publishDate']))) ?></span>
									</div><!-- featured-date -->
								</div><!-- featured-content -->
							</div><!-- current-featured -->
						</div><!-- featured-wrap -->
					</div><!-- top-featured -->
					<div class="featured-menu-cont">
						<?= $this->displayBlock('sidebar-subscribe-buttons') ?>
					</div><!-- featured-menu-cont -->
