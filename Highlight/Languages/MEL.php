<?php
/* Copyright (c)
 * - 2006-2013, Shuen-Huei Guan (drake.guan@gmail.com), highlight.js
 *              (original author)
 * - 2013,      Geert Bergman (geert@scrivo.nl), highlight.php
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "highlight.js", "highlight.php", nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * This file is a direct port of mel.js, the MEL language definition file for 
 * highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class MEL extends Language {
	
	protected function getName() {
		return "mel";
	}
	
	protected function getKeyWords() {
		return
			"int float string vector matrix if else switch case default while do for in break " .
			"continue global proc return about abs addAttr addAttributeEditorNodeHelp addDynamic " .
			"addNewShelfTab addPP addPanelCategory addPrefixToName advanceToNextDrivenKey " .
			"affectedNet affects aimConstraint air alias aliasAttr align alignCtx alignCurve " .
			"alignSurface allViewFit ambientLight angle angleBetween animCone animCurveEditor " .
			"animDisplay animView annotate appendStringArray applicationName applyAttrPreset " .
			"applyTake arcLenDimContext arcLengthDimension arclen arrayMapper art3dPaintCtx " .
			"artAttrCtx artAttrPaintVertexCtx artAttrSkinPaintCtx artAttrTool artBuildPaintMenu " .
			"artFluidAttrCtx artPuttyCtx artSelectCtx artSetPaintCtx artUserPaintCtx assignCommand " .
			"assignInputDevice assignViewportFactories attachCurve attachDeviceAttr attachSurface " .
			"attrColorSliderGrp attrCompatibility attrControlGrp attrEnumOptionMenu " .
			"attrEnumOptionMenuGrp attrFieldGrp attrFieldSliderGrp attrNavigationControlGrp " .
			"attrPresetEditWin attributeExists attributeInfo attributeMenu attributeQuery " .
			"autoKeyframe autoPlace bakeClip bakeFluidShading bakePartialHistory bakeResults " .
			"bakeSimulation basename basenameEx batchRender bessel bevel bevelPlus binMembership " .
			"bindSkin blend2 blendShape blendShapeEditor blendShapePanel blendTwoAttr blindDataType " .
			"boneLattice boundary boxDollyCtx boxZoomCtx bufferCurve buildBookmarkMenu " .
			"buildKeyframeMenu button buttonManip CBG cacheFile cacheFileCombine cacheFileMerge " .
			"cacheFileTrack camera cameraView canCreateManip canvas capitalizeString catch " .
			"catchQuiet ceil changeSubdivComponentDisplayLevel changeSubdivRegion channelBox " .
			"character characterMap characterOutlineEditor characterize chdir checkBox checkBoxGrp " .
			"checkDefaultRenderGlobals choice circle circularFillet clamp clear clearCache clip " .
			"clipEditor clipEditorCurrentTimeCtx clipSchedule clipSchedulerOutliner clipTrimBefore " .
			"closeCurve closeSurface cluster cmdFileOutput cmdScrollFieldExecuter " .
			"cmdScrollFieldReporter cmdShell coarsenSubdivSelectionList collision color " .
			"colorAtPoint colorEditor colorIndex colorIndexSliderGrp colorSliderButtonGrp " .
			"colorSliderGrp columnLayout commandEcho commandLine commandPort compactHairSystem " .
			"componentEditor compositingInterop computePolysetVolume condition cone confirmDialog " .
			"connectAttr connectControl connectDynamic connectJoint connectionInfo constrain " .
			"constrainValue constructionHistory container containsMultibyte contextInfo control " .
			"convertFromOldLayers convertIffToPsd convertLightmap convertSolidTx convertTessellation " .
			"convertUnit copyArray copyFlexor copyKey copySkinWeights cos cpButton cpCache " .
			"cpClothSet cpCollision cpConstraint cpConvClothToMesh cpForces cpGetSolverAttr cpPanel " .
			"cpProperty cpRigidCollisionFilter cpSeam cpSetEdit cpSetSolverAttr cpSolver " .
			"cpSolverTypes cpTool cpUpdateClothUVs createDisplayLayer createDrawCtx createEditor " .
			"createLayeredPsdFile createMotionField createNewShelf createNode createRenderLayer " .
			"createSubdivRegion cross crossProduct ctxAbort ctxCompletion ctxEditMode ctxTraverse " .
			"currentCtx currentTime currentTimeCtx currentUnit currentUnit curve curveAddPtCtx " .
			"curveCVCtx curveEPCtx curveEditorCtx curveIntersect curveMoveEPCtx curveOnSurface " .
			"curveSketchCtx cutKey cycleCheck cylinder dagPose date defaultLightListCheckBox " .
			"defaultNavigation defineDataServer defineVirtualDevice deformer deg_to_rad delete " .
			"deleteAttr deleteShadingGroupsAndMaterials deleteShelfTab deleteUI deleteUnusedBrushes " .
			"delrandstr detachCurve detachDeviceAttr detachSurface deviceEditor devicePanel dgInfo " .
			"dgdirty dgeval dgtimer dimWhen directKeyCtx directionalLight dirmap dirname disable " .
			"disconnectAttr disconnectJoint diskCache displacementToPoly displayAffected " .
			"displayColor displayCull displayLevelOfDetail displayPref displayRGBColor " .
			"displaySmoothness displayStats displayString displaySurface distanceDimContext " .
			"distanceDimension doBlur dolly dollyCtx dopeSheetEditor dot dotProduct " .
			"doubleProfileBirailSurface drag dragAttrContext draggerContext dropoffLocator " .
			"duplicate duplicateCurve duplicateSurface dynCache dynControl dynExport dynExpression " .
			"dynGlobals dynPaintEditor dynParticleCtx dynPref dynRelEdPanel dynRelEditor " .
			"dynamicLoad editAttrLimits editDisplayLayerGlobals editDisplayLayerMembers " .
			"editRenderLayerAdjustment editRenderLayerGlobals editRenderLayerMembers editor " .
			"editorTemplate effector emit emitter enableDevice encodeString endString endsWith env " .
			"equivalent equivalentTol erf error eval eval evalDeferred evalEcho event " .
			"exactWorldBoundingBox exclusiveLightCheckBox exec executeForEachObject exists exp " .
			"expression expressionEditorListen extendCurve extendSurface extrude fcheck fclose feof " .
			"fflush fgetline fgetword file fileBrowserDialog fileDialog fileExtension fileInfo " .
			"filetest filletCurve filter filterCurve filterExpand filterStudioImport " .
			"findAllIntersections findAnimCurves findKeyframe findMenuItem findRelatedSkinCluster " .
			"finder firstParentOf fitBspline flexor floatEq floatField floatFieldGrp floatScrollBar " .
			"floatSlider floatSlider2 floatSliderButtonGrp floatSliderGrp floor flow fluidCacheInfo " .
			"fluidEmitter fluidVoxelInfo flushUndo fmod fontDialog fopen formLayout format fprint " .
			"frameLayout fread freeFormFillet frewind fromNativePath fwrite gamma gauss " .
			"geometryConstraint getApplicationVersionAsFloat getAttr getClassification " .
			"getDefaultBrush getFileList getFluidAttr getInputDeviceRange getMayaPanelTypes " .
			"getModifiers getPanel getParticleAttr getPluginResource getenv getpid glRender " .
			"glRenderEditor globalStitch gmatch goal gotoBindPose grabColor gradientControl " .
			"gradientControlNoAttr graphDollyCtx graphSelectContext graphTrackCtx gravity grid " .
			"gridLayout group groupObjectsByName HfAddAttractorToAS HfAssignAS HfBuildEqualMap " .
			"HfBuildFurFiles HfBuildFurImages HfCancelAFR HfConnectASToHF HfCreateAttractor " .
			"HfDeleteAS HfEditAS HfPerformCreateAS HfRemoveAttractorFromAS HfSelectAttached " .
			"HfSelectAttractors HfUnAssignAS hardenPointCurve hardware hardwareRenderPanel " .
			"headsUpDisplay headsUpMessage help helpLine hermite hide hilite hitTest hotBox hotkey " .
			"hotkeyCheck hsv_to_rgb hudButton hudSlider hudSliderButton hwReflectionMap hwRender " .
			"hwRenderLoad hyperGraph hyperPanel hyperShade hypot iconTextButton iconTextCheckBox " .
			"iconTextRadioButton iconTextRadioCollection iconTextScrollList iconTextStaticLabel " .
			"ikHandle ikHandleCtx ikHandleDisplayScale ikSolver ikSplineHandleCtx ikSystem " .
			"ikSystemInfo ikfkDisplayMethod illustratorCurves image imfPlugins inheritTransform " .
			"insertJoint insertJointCtx insertKeyCtx insertKnotCurve insertKnotSurface instance " .
			"instanceable instancer intField intFieldGrp intScrollBar intSlider intSliderGrp " .
			"interToUI internalVar intersect iprEngine isAnimCurve isConnected isDirty isParentOf " .
			"isSameObject isTrue isValidObjectName isValidString isValidUiName isolateSelect " .
			"itemFilter itemFilterAttr itemFilterRender itemFilterType joint jointCluster jointCtx " .
			"jointDisplayScale jointLattice keyTangent keyframe keyframeOutliner " .
			"keyframeRegionCurrentTimeCtx keyframeRegionDirectKeyCtx keyframeRegionDollyCtx " .
			"keyframeRegionInsertKeyCtx keyframeRegionMoveKeyCtx keyframeRegionScaleKeyCtx " .
			"keyframeRegionSelectKeyCtx keyframeRegionSetKeyCtx keyframeRegionTrackCtx " .
			"keyframeStats lassoContext lattice latticeDeformKeyCtx launch launchImageEditor " .
			"layerButton layeredShaderPort layeredTexturePort layout layoutDialog lightList " .
			"lightListEditor lightListPanel lightlink lineIntersection linearPrecision linstep " .
			"listAnimatable listAttr listCameras listConnections listDeviceAttachments listHistory " .
			"listInputDeviceAxes listInputDeviceButtons listInputDevices listMenuAnnotation " .
			"listNodeTypes listPanelCategories listRelatives listSets listTransforms " .
			"listUnselected listerEditor loadFluid loadNewShelf loadPlugin " .
			"loadPluginLanguageResources loadPrefObjects localizedPanelLabel lockNode loft log " .
			"longNameOf lookThru ls lsThroughFilter lsType lsUI Mayatomr mag makeIdentity makeLive " .
			"makePaintable makeRoll makeSingleSurface makeTubeOn makebot manipMoveContext " .
			"manipMoveLimitsCtx manipOptions manipRotateContext manipRotateLimitsCtx " .
			"manipScaleContext manipScaleLimitsCtx marker match max memory menu menuBarLayout " .
			"menuEditor menuItem menuItemToShelf menuSet menuSetPref messageLine min minimizeApp " .
			"mirrorJoint modelCurrentTimeCtx modelEditor modelPanel mouse movIn movOut move " .
			"moveIKtoFK moveKeyCtx moveVertexAlongDirection multiProfileBirailSurface mute " .
			"nParticle nameCommand nameField namespace namespaceInfo newPanelItems newton nodeCast " .
			"nodeIconButton nodeOutliner nodePreset nodeType noise nonLinear normalConstraint " .
			"normalize nurbsBoolean nurbsCopyUVSet nurbsCube nurbsEditUV nurbsPlane nurbsSelect " .
			"nurbsSquare nurbsToPoly nurbsToPolygonsPref nurbsToSubdiv nurbsToSubdivPref " .
			"nurbsUVSet nurbsViewDirectionVector objExists objectCenter objectLayer objectType " .
			"objectTypeUI obsoleteProc oceanNurbsPreviewPlane offsetCurve offsetCurveOnSurface " .
			"offsetSurface openGLExtension openMayaPref optionMenu optionMenuGrp optionVar orbit " .
			"orbitCtx orientConstraint outlinerEditor outlinerPanel overrideModifier " .
			"paintEffectsDisplay pairBlend palettePort paneLayout panel panelConfiguration " .
			"panelHistory paramDimContext paramDimension paramLocator parent parentConstraint " .
			"particle particleExists particleInstancer particleRenderInfo partition pasteKey " .
			"pathAnimation pause pclose percent performanceOptions pfxstrokes pickWalk picture " .
			"pixelMove planarSrf plane play playbackOptions playblast plugAttr plugNode pluginInfo " .
			"pluginResourceUtil pointConstraint pointCurveConstraint pointLight pointMatrixMult " .
			"pointOnCurve pointOnSurface pointPosition poleVectorConstraint polyAppend " .
			"polyAppendFacetCtx polyAppendVertex polyAutoProjection polyAverageNormal " .
			"polyAverageVertex polyBevel polyBlendColor polyBlindData polyBoolOp polyBridgeEdge " .
			"polyCacheMonitor polyCheck polyChipOff polyClipboard polyCloseBorder polyCollapseEdge " .
			"polyCollapseFacet polyColorBlindData polyColorDel polyColorPerVertex polyColorSet " .
			"polyCompare polyCone polyCopyUV polyCrease polyCreaseCtx polyCreateFacet " .
			"polyCreateFacetCtx polyCube polyCut polyCutCtx polyCylinder polyCylindricalProjection " .
			"polyDelEdge polyDelFacet polyDelVertex polyDuplicateAndConnect polyDuplicateEdge " .
			"polyEditUV polyEditUVShell polyEvaluate polyExtrudeEdge polyExtrudeFacet " .
			"polyExtrudeVertex polyFlipEdge polyFlipUV polyForceUV polyGeoSampler polyHelix " .
			"polyInfo polyInstallAction polyLayoutUV polyListComponentConversion polyMapCut " .
			"polyMapDel polyMapSew polyMapSewMove polyMergeEdge polyMergeEdgeCtx polyMergeFacet " .
			"polyMergeFacetCtx polyMergeUV polyMergeVertex polyMirrorFace polyMoveEdge " .
			"polyMoveFacet polyMoveFacetUV polyMoveUV polyMoveVertex polyNormal polyNormalPerVertex " .
			"polyNormalizeUV polyOptUvs polyOptions polyOutput polyPipe polyPlanarProjection " .
			"polyPlane polyPlatonicSolid polyPoke polyPrimitive polyPrism polyProjection " .
			"polyPyramid polyQuad polyQueryBlindData polyReduce polySelect polySelectConstraint " .
			"polySelectConstraintMonitor polySelectCtx polySelectEditCtx polySeparate " .
			"polySetToFaceNormal polySewEdge polyShortestPathCtx polySmooth polySoftEdge " .
			"polySphere polySphericalProjection polySplit polySplitCtx polySplitEdge polySplitRing " .
			"polySplitVertex polyStraightenUVBorder polySubdivideEdge polySubdivideFacet " .
			"polyToSubdiv polyTorus polyTransfer polyTriangulate polyUVSet polyUnite polyWedgeFace " .
			"popen popupMenu pose pow preloadRefEd print progressBar progressWindow projFileViewer " .
			"projectCurve projectTangent projectionContext projectionManip promptDialog propModCtx " .
			"propMove psdChannelOutliner psdEditTextureFile psdExport psdTextureFile putenv pwd " .
			"python querySubdiv quit rad_to_deg radial radioButton radioButtonGrp radioCollection " .
			"radioMenuItemCollection rampColorPort rand randomizeFollicles randstate rangeControl " .
			"readTake rebuildCurve rebuildSurface recordAttr recordDevice redo reference " .
			"referenceEdit referenceQuery refineSubdivSelectionList refresh refreshAE " .
			"registerPluginResource rehash reloadImage removeJoint removeMultiInstance " .
			"removePanelCategory rename renameAttr renameSelectionList renameUI render " .
			"renderGlobalsNode renderInfo renderLayerButton renderLayerParent " .
			"renderLayerPostProcess renderLayerUnparent renderManip renderPartition " .
			"renderQualityNode renderSettings renderThumbnailUpdate renderWindowEditor " .
			"renderWindowSelectContext renderer reorder reorderDeformers requires reroot " .
			"resampleFluid resetAE resetPfxToPolyCamera resetTool resolutionNode retarget " .
			"reverseCurve reverseSurface revolve rgb_to_hsv rigidBody rigidSolver roll rollCtx " .
			"rootOf rot rotate rotationInterpolation roundConstantRadius rowColumnLayout rowLayout " .
			"runTimeCommand runup sampleImage saveAllShelves saveAttrPreset saveFluid saveImage " .
			"saveInitialState saveMenu savePrefObjects savePrefs saveShelf saveToolSettings scale " .
			"scaleBrushBrightness scaleComponents scaleConstraint scaleKey scaleKeyCtx sceneEditor " .
			"sceneUIReplacement scmh scriptCtx scriptEditorInfo scriptJob scriptNode scriptTable " .
			"scriptToShelf scriptedPanel scriptedPanelType scrollField scrollLayout sculpt " .
			"searchPathArray seed selLoadSettings select selectContext selectCurveCV selectKey " .
			"selectKeyCtx selectKeyframeRegionCtx selectMode selectPref selectPriority selectType " .
			"selectedNodes selectionConnection separator setAttr setAttrEnumResource " .
			"setAttrMapping setAttrNiceNameResource setConstraintRestPosition " .
			"setDefaultShadingGroup setDrivenKeyframe setDynamic setEditCtx setEditor setFluidAttr " .
			"setFocus setInfinity setInputDeviceMapping setKeyCtx setKeyPath setKeyframe " .
			"setKeyframeBlendshapeTargetWts setMenuMode setNodeNiceNameResource setNodeTypeFlag " .
			"setParent setParticleAttr setPfxToPolyCamera setPluginResource setProject " .
			"setStampDensity setStartupMessage setState setToolTo setUITemplate setXformManip sets " .
			"shadingConnection shadingGeometryRelCtx shadingLightRelCtx shadingNetworkCompare " .
			"shadingNode shapeCompare shelfButton shelfLayout shelfTabLayout shellField " .
			"shortNameOf showHelp showHidden showManipCtx showSelectionInTitle " .
			"showShadingGroupAttrEditor showWindow sign simplify sin singleProfileBirailSurface " .
			"size sizeBytes skinCluster skinPercent smoothCurve smoothTangentSurface smoothstep " .
			"snap2to2 snapKey snapMode snapTogetherCtx snapshot soft softMod softModCtx sort sound " .
			"soundControl source spaceLocator sphere sphrand spotLight spotLightPreviewPort " .
			"spreadSheetEditor spring sqrt squareSurface srtContext stackTrace startString " .
			"startsWith stitchAndExplodeShell stitchSurface stitchSurfacePoints strcmp " .
			"stringArrayCatenate stringArrayContains stringArrayCount stringArrayInsertAtIndex " .
			"stringArrayIntersector stringArrayRemove stringArrayRemoveAtIndex " .
			"stringArrayRemoveDuplicates stringArrayRemoveExact stringArrayToString " .
			"stringToStringArray strip stripPrefixFromName stroke subdAutoProjection " .
			"subdCleanTopology subdCollapse subdDuplicateAndConnect subdEditUV " .
			"subdListComponentConversion subdMapCut subdMapSewMove subdMatchTopology subdMirror " .
			"subdToBlind subdToPoly subdTransferUVsToCache subdiv subdivCrease " .
			"subdivDisplaySmoothness substitute substituteAllString substituteGeometry substring " .
			"surface surfaceSampler surfaceShaderList swatchDisplayPort switchTable symbolButton " .
			"symbolCheckBox sysFile system tabLayout tan tangentConstraint texLatticeDeformContext " .
			"texManipContext texMoveContext texMoveUVShellContext texRotateContext texScaleContext " .
			"texSelectContext texSelectShortestPathCtx texSmudgeUVContext texWinToolCtx text " .
			"textCurves textField textFieldButtonGrp textFieldGrp textManip textScrollList " .
			"textToShelf textureDisplacePlane textureHairColor texturePlacementContext " .
			"textureWindow threadCount threePointArcCtx timeControl timePort timerX toNativePath " .
			"toggle toggleAxis toggleWindowVisibility tokenize tokenizeList tolerance tolower " .
			"toolButton toolCollection toolDropped toolHasOptions toolPropertyWindow torus toupper " .
			"trace track trackCtx transferAttributes transformCompare transformLimits translator " .
			"trim trunc truncateFluidCache truncateHairCache tumble tumbleCtx turbulence " .
			"twoPointArcCtx uiRes uiTemplate unassignInputDevice undo undoInfo ungroup uniform unit " .
			"unloadPlugin untangleUV untitledFileName untrim upAxis updateAE userCtx uvLink " .
			"uvSnapshot validateShelfName vectorize view2dToolCtx viewCamera viewClipPlane " .
			"viewFit viewHeadOn viewLookAt viewManip viewPlace viewSet visor volumeAxis vortex " .
			"waitCursor warning webBrowser webBrowserPrefs whatIs window windowPref wire " .
			"wireContext workspace wrinkle wrinkleContext writeTake xbmLangPathList xform";
	}
	
	protected function getIllegal() {
		return "<\/";
	}
	
	protected function getContainedModes() {
		
		return array(
			$this->C_NUMBER_MODE,
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "string",
				"begin" => "`", 
				"end" => "`",
				"contains" => array(
					$this->BACKSLASH_ESCAPE
				)
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "\$\d",
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "[\$\%\@\*](\^\w\b|#\w+|[^\s\w{]|{\w+}|\w+)"
			)),
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE
		);
		
	}

}

?>