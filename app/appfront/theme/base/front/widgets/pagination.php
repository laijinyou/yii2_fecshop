<div class="bg-gray-50 text-center border border-gray-200">
    <ul class="inline-flex -space-x-px py-6">
        <?php if($prevPage): ?>
            <li>
                <a href="<?= $prevPage['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3">前のページ</a>
            </li>
        <?php endif; ?>

        <?php if($firstSpaceShow): ?>
            <li>
                <a href="<?= $firstSpaceShow['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3"><?= $firstSpaceShow[$pageParam] ?></a>
            </li>
        <?php endif; ?>

        <?php if($hiddenFrontStr): ?>
            <li>
                <a href="javascript:void(0);" class="bg-gray-50 border-x border-gray-300 text-gray-500 py-2 px-3 cursor-default"><?= $hiddenFrontStr ?></a>
            </li>
        <?php endif; ?>

        <?php if(!empty($frontPage )): ?>
            <?php foreach($frontPage as $page): ?>
                <li>
                    <a href="<?= $page['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3"><?= $page[$pageParam] ?></a>
                </li>
            <?php endforeach; ?>	
        <?php endif; ?>

        <?php if($currentPage): ?>
            <li>
                <a href="javascript:void(0);" class="bg-gray-100 border border-gray-300 text-gray-500 py-2 px-3 cursor-default"><?= $currentPage[$pageParam] ?></a>
            </li>
        <?php endif; ?>

        <?php if(!empty($behindPage )): ?>
            <?php foreach($behindPage as $page): ?>
                <li>
                    <a href="<?= $page['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3"><?= $page[$pageParam] ?></a>
                </li>
            <?php endforeach;  ?>	
        <?php endif; ?>

        <?php if($hiddenBehindStr): ?>
            <li>
                <a href="javascript:void(0);" class="bg-gray-50 border-x border-gray-300 text-gray-500 py-2 px-3 cursor-default"><?= $hiddenBehindStr ?></a>
            </li>
        <?php endif; ?>

        <?php if($lastSpaceShow): ?>
            <li>
                <a href="<?= $lastSpaceShow['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3"><?= $lastSpaceShow[$pageParam] ?></a>
            </li>
        <?php endif; ?>
        
        
        <?php if($nextPage): ?>
            <li>
                <a href="<?= $nextPage['url']['url'] ?>" class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-red-500 leading-tight py-2 px-3">次のページ</a>
            </li>
        <?php endif; ?>

    </ul>
</div>
