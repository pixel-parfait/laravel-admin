import { Node, mergeAttributes } from '@tiptap/core'

const Paragraph = Node.create({
    name: 'inlineParagraph',
    priority: 1000,
    defaultOptions: {
        HTMLAttributes: {},
    },
    group: 'block',
    content: 'inline*',
    parseHTML() {
        return [
            { tag: 'span' },
        ]
    },
    renderHTML({ HTMLAttributes }) {
        return ['span', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
    },
    addCommands() {
        return {
            setParagraph: () => ({ commands }) => {
                return commands.toggleNode('inlineParagraph', 'inlineParagraph')
            },
        }
    },
});

export default Paragraph;
