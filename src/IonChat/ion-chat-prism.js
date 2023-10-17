function doIt() {
    console.log("ion-chat-prism.js is here");
    jQuery('.bm-message-content').each(function() {
        var content = jQuery(this).text();
        var codeBlocks = content.match(/```([\s\S]*?)```/g);
        
        if (codeBlocks) {
            codeBlocks.forEach(function(block) {
                var code = block.replace(/```/g, '');
                var highlightedCode = Prism.highlight(code, Prism.languages.javascript, 'javascript');
                var formattedBlock = '<pre><code class="language-javascript">' + highlightedCode + '</code></pre>';
                
                content = content.replace(block, formattedBlock);
            });
            
            jQuery(this).html(content);
        }
    });
}