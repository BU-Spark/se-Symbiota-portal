<?php
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.en.php');
?>
	
<script src="../../js/symb/collections.editor.imgtools.js?ver=3" type="text/javascript"></script>
<script>
	function quickEntryOcrImage(ocrButton, imgidVar, imgCnt) {
		console.log("Function quickEntryOcrImage called");
		imgCnt = 0; // Reset image counter
		ocrButton.disabled = true; // Disable button to prevent multiple clicks

		// Show loading spinner
		let wcElem = document.getElementById("workingcircle-tess-" + imgCnt);
		wcElem.style.display = "inline";

		// Get selected OCR method
		let target = document.querySelector('input[name="ocr-method"]:checked').value;

		if (target === "external") {
			// External OCR API call
			console.log("external chosen");
			$.ajax({
				type: "POST",
				url: "../quickentry/rpc/externalocr.php",
				data: { imgid: imgidVar },
				success: function(response) {
					// Ensure response is parsed as JSON
					let decodedResponse;
					if (typeof response === "string") {
						try {
							decodedResponse = JSON.parse(response);
						} catch (e) {
							console.error("Error parsing JSON response:", e);
							decodedResponse = response; // Use raw response if JSON parsing fails
						}
					} else {
						decodedResponse = response;
					}

					// Format the JSON object into plain text
					let plainTextResponse = "";
					if (typeof decodedResponse === "object") {
						for (const [key, value] of Object.entries(decodedResponse)) {
							plainTextResponse += `${key}: ${value}\n`;
						}
					} else {
						plainTextResponse = decodedResponse;
					}

					// Update the textarea with formatted text
					let rawtextBox = document.getElementById("rawtext");
					console.log("rawtextBox:", rawtextBox);
					rawtextBox.value = plainTextResponse; // Use 'value' to set the content of the <textarea>

					wcElem.style.display = "none";
					ocrButton.disabled = false;
				},
				error: function(xhr, status, error) {
					console.error("External OCR Error: ", error);
					wcElem.style.display = "none";
					ocrButton.disabled = false;
				}
			});
		} else {
			console.log("Tesseract chosen");
			// Tesseract OCR API call
			$.ajax({
				type: "POST",
				url: "../quickentry/rpc/ocrimage.php",
				data: { imgid: imgidVar, target: target },
				success: function(msg) {
					let rawStr = msg;
					let addform = document.getElementById("ocraddform");

					
					addform.rawtext.innerText = rawStr;
					addform.rawtext.textContent = rawStr;

					// Add source information
					let today = new Date();
					let dd = today.getDate();
					let mm = today.getMonth() + 1;
					let yyyy = today.getFullYear();
					if (dd < 10) dd = '0' + dd;
					if (mm < 10) mm = '0' + mm;
					addform.rawsource.value = "Tesseract: " + yyyy + "-" + mm + "-" + dd;

					// Hide spinner and re-enable button
					wcElem.style.display = "none";
					ocrButton.disabled = false;
				}
			});
		}
	}


	function nextProcessingImage() {
		var imgArr = <?php echo json_encode($imgUrlCollection); ?>;
		var currentImageIndex = parseInt(document.getElementById('current-image-index').textContent);
		var totalImages = imgArr.length;
		var nextImageIndex = (currentImageIndex + 1) % totalImages; // This ensures the index loops back to 0

		// Correctly reference the new image URL from the JavaScript array
		var newImgSrc = imgArr[nextImageIndex]; // This should be the URL of the next image

		// Update the display of the current image index and count
		document.getElementById('current-image-index').textContent = nextImageIndex;
		document.getElementById('image-count').textContent = 'Image ' + (nextImageIndex + 1) + ' of ' + totalImages;
		document.getElementById('activeimg').src = newImgSrc; // Set the new image source

		// Optionally update the onload event for the new image
		document.getElementById('activeimg').onload = function() {
			initImageTool('activeimg-' + nextImageIndex); // You might need to adjust this if it uses the image ID dynamically
		};

		return false; // Prevent the default behavior of the link
	}
	$(function() {
		$( "#zoomInfoDialog" ).dialog({
			autoOpen: false,
			position: { my: "left top", at: "right bottom", of: "#zoomInfoDiv" }
		});

		$( "#zoomInfoDiv" ).click(function() {
			$( "#zoomInfoDialog" ).dialog( "open" );
		});
	});
	function rotateImage(rotationAngle){
		var imgObj = document.getElementById("activeimg-0");
		var imgAngle = 0;
		if(imgObj.style.transform){
			var transformValue = imgObj.style.transform;
			imgAngle = parseInt(transformValue.substring(7));
		}
		imgAngle = imgAngle + rotationAngle;
		if(imgAngle < 0) imgAngle = 360 + imgAngle;
		else if(imgAngle == 360) imgAngle = 0;
		imgObj.style.transform = "rotate("+imgAngle+"deg)";
		$(imgObj).imagetool("option","rotationAngle",imgAngle);
		$(imgObj).imagetool("reset");
	}

	function floatImgPanel(){
		$( "#labelProcFieldset" ).css('position', 'fixed');
		$( "#labelProcFieldset" ).css('top', '20px');
		var pos = $( "#labelProcDiv" ).position();
		var posLeft = pos.left - $(window).scrollLeft();
		$( "#labelProcFieldset" ).css('left', posLeft);
		$( "#floatImgDiv" ).hide();
		$( "#draggableImgDiv" ).hide();
		$( "#anchorImgDiv" ).show();
	}

	function draggableImgPanel(){
		$( "#labelProcFieldset" ).draggable();
		$( "#labelProcFieldset" ).draggable({ cancel: "#labelprocessingdiv" });
		$( "#labelHeaderDiv" ).css('cursor', 'move');
		$( "#labelProcFieldset" ).css('top', '10px');
		$( "#labelProcFieldset" ).css('left', '5px');
		$( "#floatImgDiv" ).hide();
		$( "#draggableImgDiv" ).hide();
		$( "#anchorImgDiv" ).show();
	}

	function anchorImgPanel(){
		$( "#draggableImgDiv" ).show();
		$( "#floatImgDiv" ).show();
		$( "#anchorImgDiv" ).hide();
		$( "#labelProcFieldset" ).css('position', 'static');
		$( "#labelProcFieldset" ).css('top', '');
		$( "#labelProcFieldset" ).css('left', '');
		try {
			$( "#labelProcFieldset" ).draggable( "destroy" );
			$( "#labelHeaderDiv" ).css('cursor', 'default');
		}
		catch(err) {
		}
	}
</script>
<style>
	.ocr-box{ padding: 5px; float:left; }
	.ocr-box button{ margin: 5px; }
</style>
<div id="labelProcDiv" style="width:100%;height:425px;position:relative;">
	<fieldset id="labelProcFieldset" style="background-color:#F2F2F3;">
		<div id="labelHeaderDiv" style="margin-top:0px;height:15px;position:relative">
			<div style="float:left;margin-top:3px;margin-right:15px"><a id="zoomInfoDiv" href="#"><?php echo $LANG['ZOOM']; ?></a></div>
			<div id="zoomInfoDialog" style="background-color:white;">
				<?php echo $LANG['ZOOM_DIRECTIONS']; ?>
			</div>
			<div style="float:left;margin-right:15px">
				<div id="draggableImgDiv" style="float:left" title="<?php echo $LANG['MAKE_DRAGGABLE']; ?>"><a href="#" onclick="draggableImgPanel()"><img src="../../images/draggable.png" style="width:15px" /></a></div>
				<div id="anchorImgDiv" style="float:left;margin-left:10px;display:none" title="<?php echo $LANG['ANCHOR_IMG']; ?>"><a href="#" onclick="anchorImgPanel()"><img src="../../images/anchor.png" style="width:15px" /></a></div>
			</div>
			<div style="float:left;;padding-right:10px;margin:2px 20px 0px 0px;"><?php echo $LANG['ROTATE']; ?>: <a href="#" onclick="rotateImage(-90)">&nbsp;L&nbsp;</a> &lt;&gt; <a href="#" onclick="rotateImage(90)">&nbsp;R&nbsp;</a></div>
		</div>
		<div id="labelprocessingdiv" style="clear:both;">
			<?php $currentImageId = 0; ?>
			<div id="labeldiv-<?php echo $currentImageId; ?>">
				<div>
					<img id="activeimg-<?php echo $currentImageId; ?>" src="<?php echo($imgUrlCollection[$currentImageId]) ?>" style="height:400px;" onload="initImageTool('activeimg-<?php echo $currentImageId; ?>')" />
				</div>
				<div style="width:100%; clear:both;">
					<div style="float:right; margin-right:20px; font-weight:bold;">
						<span id="current-image-index" style="display:none;"><?php echo $currentImageId; ?></span>
						<span id="image-count">Image <?php echo ($currentImageId + 1); ?> of <?php echo count($imgUrlCollection); ?></span>
						<?php if(count($imgUrlCollection) > 1): ?>
							<a href="#" onclick="return nextProcessingImage();">>&gt;</a>
						<?php endif; ?>
					</div>
				</div>
				<div style="width:100%;clear:both;">
					<h4 style="text-align:left;">
						Choose your OCR model
					</h4>
					<fieldset class="" style="text-align:left; margin-bottom:15px">
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
							<div>
								<label>
									<input type="radio" name="ocr-method" id="ocr-method-tess" value="tess" checked />
									<?php echo ("Tesseract OCR"); ?>
								</label><br />
								<label>
									<input type="radio" name="ocr-method" id="ocr-method-external" value="external" />
									<?php echo ("External OCR"); ?>
								</label>
							</div>
							<div>
								<label>
									<input type="checkbox" id="ocr-full" value="1" />
									<?php echo $LANG['OCR_WHOLE_IMG']; ?>
								</label><br />
								<label>
									<input type="checkbox" id="ocr-analysis" value="1" />
									<?php echo $LANG['OCR_ANALYSIS']; ?>
								</label>
							</div>
						</div>
						<div style="margin-top:15px">
							<button 
								value="OCR Image" 
								onclick="quickEntryOcrImage(this, <?php echo $imgId; ?>, <?php echo $currentImageId; ?>);">
								<?php echo $LANG['OCR_IMAGE']; ?>
							</button>
							<img id="workingcircle-tess-<?php echo $currentImageId; ?>" src="../../images/workingcircle.gif" style="display:none;" />
						</div>
					</fieldset>
					<?php
					if(!empty($DIGILEAP_OCR_ACTIVATED)){
						?>
						<fieldset class="ocr-box">
							<legend>DigiLeap OCR</legend>
							<input type="checkbox" id="ocrfull-digi" value="1" /> <?php echo $LANG['OCR_WHOLE_IMG']; ?><br/>
							<div>
								<button 
									value="OCR Image" 
									onclick="quickEntryOcrImage(this, 'tess', <?php echo $imgId; ?>, <?php echo $currentImageId; ?>);">
									<?php echo $LANG['OCR_IMAGE']; ?>
								</button>
								<img id="workingcircle-digi-<?php echo $currentImageId; ?>" src="../../images/workingcircle.gif" style="display:none;" />
							</div>
						</fieldset>
						<?php
					}
					?>
				</div>
				<div style="width:100%;clear:both;">
					<div id="QEtfadddiv-<?php echo $currentImageId; ?>" style="">
						<form id="quickocraddform-<?php echo $currentImageId; ?>" name="ocraddform-<?php echo $imgId; ?>" method="post" action="occurrencequickentry.php">
							<div>
								<textarea id="rawtext" name="rawtext" rows="12" cols="48" style="width:97%;background-color:#F8F8F8;"></textarea>
							</div>
							<div title="OCR Notes" style="text-align:left; margin-top:10px">
								<b><?php echo $LANG['NOTES']; ?>:</b>
								<input name="rawnotes" type="text" value="" style="width:97%;" />
							</div>
							<div title="OCR Source" style="text-align:left;">
								<b><?php echo $LANG['SOURCE']; ?>:</b>
								<input name="rawsource" type="text" value="" style="width:97%;" />
							</div>
							<div style="float:left">
								<input type="hidden" name="imgid" value="<?php echo $imgId; ?>" />
								<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
								<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
								<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
								<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
								<button name="submitaction" type="submit" value="Save OCR" style="margin-top:10px;"><?php echo $LANG['SAVE_OCR']; ?></button>
							</div>
						</form>
						<div style="font-weight:bold;float:right;"><?php echo '&lt;'.$LANG['NEW'].'&gt; '.$LANG['OF'].' '.count($fArr); ?>
						</div>
					</div>
					<div id="QEtfeditdiv-<?php echo $currentImageId; ?>" style="clear:both;">
						<?php
						if(array_key_exists($imgId,$fragArr)){
							$fragCnt = 1;
							$targetPrlid = '';
							if(isset($newPrlid) && $newPrlid) $targetPrlid = $newPrlid;
							if(array_key_exists('editprlid',$_REQUEST)) $targetPrlid = $_REQUEST['editprlid'];
							foreach($fArr as $prlid => $rArr){
								$displayBlock = 'none';
								if($targetPrlid){
									if($prlid == $targetPrlid){
										$displayBlock = 'block';
									}
								}
								elseif($fragCnt==1){
									$displayBlock = 'block';
								}
								?>
								<div id="tfdiv-<?php echo $currentImageId.'-'.$fragCnt; ?>" style="display:<?php echo $displayBlock; ?>;">
									<form id="tfeditform-<?php echo $prlid; ?>" name="tfeditform-<?php echo $prlid; ?>" method="post" action="occurrenceeditor.php">
										<div>
											<textarea name="rawtext" rows="12" cols="48" style="width:97%"><?php echo $rArr['raw']; ?></textarea>
										</div>
										<div title="OCR Notes" style="text-align:left;">
											<b><?php echo $LANG['NOTES']; ?>:</b>
											<input name="rawnotes" type="text" value="<?php echo $rArr['notes']; ?>" style="width:97%;" />
										</div>
										<div title="OCR Source" style="text-align:left;">
											<b><?php echo $LANG['SOURCE']; ?>:</b>
											<input name="rawsource" type="text" value="<?php echo $rArr['source']; ?>" style="width:97%;" />
										</div>
										<div style="float:left;margin-left:10px;">
											<input type="hidden" name="editprlid" value="<?php echo $prlid; ?>" />
											<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
											<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
											<button name="submitaction" type="submit" value="Save OCR Edits" ><?php echo $LANG['SAVE_OCR_EDITS']; ?></button>
										</div>
										<div style="float:left;margin-left:20px;">
											<input type="hidden" name="iurl" value="<?php echo $iUrl; ?>" />
											<input type="hidden" id="cnumber" name="cnumber" value="<?php echo array_key_exists('catalognumber',$occArr)?$occArr['catalognumber']:''; ?>" />
											<?php
											if(isset($NLP_SALIX_ACTIVATED) && $NLP_SALIX_ACTIVATED){
												echo '<input name="salixocr" type="button" value="SALIX Parser" onclick="nlpSalix(this,'.$prlid.')" />';
												echo '<img id="workingcircle_salix-'.$prlid.'" src="../../images/workingcircle.gif" style="display:none;" />';
											}
											if(isset($NLP_LBCC_ACTIVATED) && $NLP_LBCC_ACTIVATED){
												echo '<input id="nlplbccbutton" name="nlplbccbutton" type="button" value="LBCC Parser" onclick="nlpLbcc(this,'.$prlid.')" />';
												echo '<img id="workingcircle_lbcc-'.$prlid.'" src="../../images/workingcircle.gif" style="display:none;" />';
											}
											?>
										</div>
									</form>
									<div style="float:right;font-weight:bold;margin-right:20px;">
										<?php
										echo $fragCnt.' of '.count($fArr);
										if(count($fArr) > 1){
											?>
											<a href="#" onclick="return nextRawText(<?php echo $currentImageId.','.($fragCnt+1); ?>)">=&gt;&gt;</a>
											<?php
										}
										?>
									</div>
									<div style="clear:both;">
										<form name="tfdelform-<?php echo $prlid; ?>" method="post" action="occurrenceeditor.php" style="margin-left:10px;width:100px;" >
											<input type="hidden" name="delprlid" value="<?php echo $prlid; ?>" />
											<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
											<input type="hidden" name="occid" value="<?php echo $occId; ?>" /><br/>
											<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
											<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
											<button name="submitaction" type="submit" value="Delete OCR" ><?php echo $LANG['DELETE_OCR']; ?></button>
										</form>
									</div>
								</div>
								<?php
								$fragCnt++;
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>